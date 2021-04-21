<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderLineItem;
use App\Models\OrderAddressEvent;
use OwenIt\Auditing\Models\Audit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogsController extends Controller
{
    const AVAILABLE_AUDITS = [
        'order' => Order::class,
        'order_address_event' => OrderAddressEvent::class,
        'order_line_item' => OrderLineItem::class,
    ];

    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', Audit::class);

        $data = $request->validate([
            'model_id' => 'required|integer',
            'model_type' => 'required|in:'.collect(self::AVAILABLE_AUDITS)->keys()->implode(','),
        ]);

        return JsonResource::collection($this->getAuditLogData($data['model_id'], $data['model_type']));
    }

    protected function getAuditLogData($modelId, $modelType)
    {
        if ($modelType === 'order') {
            $order = Order::query()
                ->select('id')
                ->with([
                    'audits.user',
                    'orderAddressEvents:id,t_order_id',
                    'orderAddressEvents.audits.user',
                    'orderLineItems:id,t_order_id',
                    'orderLineItems.audits.user',
                ])
                ->findOrFail($modelId);

            return [
                'order' => $order->getAttributesChanges(),
                'order_address_events' => $order->orderAddressEvents->map(function ($orderAddressEvent) {
                    return [
                        'id' => $orderAddressEvent->id,
                        'audits' => $orderAddressEvent->getAttributesChanges(),
                    ];
                }),
                'order_line_items' => $order->orderLineItems->map(function ($orderLineItem) {
                    return [
                        'id' => $orderLineItem->id,
                        'audits' => $orderLineItem->getAttributesChanges(),
                    ];
                }),
            ];
        }

        $modelClass = self::AVAILABLE_AUDITS[$modelType];
        $modelInstance = $modelClass::select('id')->with('audits')->find($modelId);

        return [
            $modelType => $modelInstance ? $modelInstance->getAttributesChanges() : [],
        ];
    }
}
