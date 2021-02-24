<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderLineItem;
use App\Models\OrderAddressEvent;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Collection;
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
                ->find($modelId);

            return [
                'order' => $this->mapToAttributeChanges($order->audits),
                'order_address_events' => $order->orderAddressEvents->map(function ($orderAddressEvent) {
                    return [
                        'id' => $orderAddressEvent->id,
                        'audits' => $this->mapToAttributeChanges($orderAddressEvent->audits),
                    ];
                }),
                'order_line_items' => $order->orderLineItems->map(function ($orderLineItem) {
                    return [
                        'id' => $orderLineItem->id,
                        'audits' => $this->mapToAttributeChanges($orderLineItem->audits),
                    ];
                }),
            ];
        }

        $modelClass = self::AVAILABLE_AUDITS[$modelType];
        $data = $modelClass::select('id')->with('audits')->find($modelId);

        return [
            $modelType => $this->mapToAttributeChanges($data->audits),
        ];
    }

    protected function mapToAttributeChanges(\Illuminate\Database\Eloquent\Collection $audits)
    {
        return $audits
            ->flatMap(fn ($audit) => $this->getModifiedAudit($audit))
            ->groupBy('attribute')
            ->map(function ($changes) {
                return collect($changes)->sortBy(function ($change) {
                    return $change['updated_at']->getTimestamp();
                });
            });
    }

    protected function getModifiedAudit(Audit $audit): Collection
    {
        return collect($audit->getModified())
            ->map(function ($modified, $attribute) use ($audit) {
                return $modified + [
                    'old' => $audit->old,
                    'user' => $audit->user->name ?? null,
                    'attribute' => $attribute,
                    'updated_at' => $audit->created_at,
                ];
            })
            ->values();
    }
}
