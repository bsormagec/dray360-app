<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Resources\SideBySideOrder;
use App\Http\Requests\UpdateOrderRequest;

class UpdateAllOrdersController extends Controller
{
    const BLACKLISTED_PARAMETERS = [
        'unit_number',
        'seal_number',
    ];

    public function __invoke(UpdateOrderRequest $request, Order $order)
    {
        $this->authorize('updateAll', $order);
        $orderData = collect($request->validated())
            ->reject(fn ($item, $key) => in_array($key, self::BLACKLISTED_PARAMETERS))
            ->toArray();
        $relatedModels = $request->validate([
            'order_line_items' => ['sometimes', 'array'],
            'order_line_items.*.t_order_id' => ['required', "in:{$order->id}"],
            'order_line_items.*.id' => 'present',
            'order_line_items.*.deleted_at' => 'sometimes',
            'order_address_events' => ['sometimes', 'array'],
            'order_address_events.*.id' => 'present',
            'order_address_events.*.t_order_id' => ['required', "in:{$order->id}"],
            'order_address_events.*.t_address_id' => ['nullable', 'exists:t_addresses,id'],
        ]);

        $order->updateRelatedModels($relatedModels);
        $orderId = $order->id;

        Order::query()
            ->where('request_id', $order->request_id)
            ->with(['orderAddressEvents', 'orderLineItems'])
            ->get()
            ->each(function ($order) use ($orderData, $relatedModels, $orderId, $request) {
                $order->update($orderData);
                $order->updateEquipmentTypesDictionaryItems($orderData);

                if ($order->id === $orderId) {
                    return;
                }

                $this->updateOrderAddressEvents($order, $relatedModels);
                $this->updateOrderLineItems($order, $relatedModels, $request->get('change_path'));
            });

        $order = Order::getBasicOrderForSideBySide($order->id);

        return new SideBySideOrder($order, false);
    }

    protected function updateOrderAddressEvents($order, $relatedModels): void
    {
        if (! isset($relatedModels['order_address_events'])) {
            return;
        }

        foreach ($relatedModels['order_address_events'] as $index => $orderAddressEvent) {
            $order->orderAddressEvents->each(function ($event) use ($index, $orderAddressEvent) {
                $areEqual = $index + 1 == $event->event_number &&
                $event->is_hook_event == $orderAddressEvent['is_hook_event'] &&
                $event->is_mount_event == $orderAddressEvent['is_mount_event'] &&
                $event->is_deliver_event == $orderAddressEvent['is_deliver_event'] &&
                $event->is_dismount_event == $orderAddressEvent['is_dismount_event'] &&
                $event->is_drop_event == $orderAddressEvent['is_drop_event'] &&
                $event->is_pickup_event == $orderAddressEvent['is_pickup_event'];

                if ($areEqual && $orderAddressEvent['t_address_id']) {
                    $event->update([
                        't_address_id' => $orderAddressEvent['t_address_id'],
                        'note' => $orderAddressEvent['note'],
                        't_address_verified' => true,
                    ]);
                }
            });
        }
    }

    protected function updateOrderLineItems($order, $relatedModels, $changePath = null): void
    {
        if (! isset($relatedModels['order_line_items']) || ! $changePath) {
            return;
        }
        $propertyChanged = Str::afterLast($changePath, '.');

        if ($propertyChanged == '') {
            return;
        }

        foreach ($relatedModels['order_line_items'] as $index => $orderLineItem) {
            $order->orderLineItems->each(function ($item, $i) use ($index, $orderLineItem, $propertyChanged) {
                if ($i == $index) {
                    $item->update([
                        $propertyChanged => $orderLineItem[$propertyChanged],
                    ]);
                }
            });
        }
    }
}
