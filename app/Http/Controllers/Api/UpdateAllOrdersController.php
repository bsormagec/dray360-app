<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SideBySideOrder;

class UpdateAllOrdersController extends Controller
{
    const BLACKLISTED_PARAMETERS = [
        'unit_number',
        'seal_number',
    ];

    public function __invoke(Request $request, Order $order)
    {
        $this->authorize('updateAll', $order);
        $orderData = collect($request->validate(Order::$rules))
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
            ->with('orderAddressEvents')
            ->get()
            ->each(function ($order) use ($orderData, $relatedModels, $orderId) {
                $order->update($orderData);

                if ($order->id === $orderId) {
                    return;
                }
                if (! ($relatedModels['order_address_events'] ?? false)) {
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
            });

        $order = Order::getBasicOrderForSideBySide($order->id);

        return new SideBySideOrder($order, false);
    }
}
