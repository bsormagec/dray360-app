<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        $statuses = OCRRequestStatus::query()
            ->select(['id', 'created_at', 'status', 'status_date'])
            ->when($request->get('system_status'), function ($query) {
                return $query->addSelect('status_metadata');
            })
            ->where(function ($query) use ($order) {
                $query->where('request_id', $order->request_id)
                    ->whereNull('order_id');
            })
            ->orWhere('order_id', $order->id)
            ->orderBy('order_id')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($item, $key) use ($request) {
                if (! $request->get('system_status')) {
                    return $item->display_status;
                }

                if ($item->status == OCRRequestStatus::OCR_WAITING) {
                    return $item->status;
                }

                return $item->status . "#{$key}";
            })
            ->map(function ($groupedStatuses, $status) use ($request) {
                $groupedStatuses = $groupedStatuses->sortBy('id');
                return [
                    'status' => $request->get('system_status') ? Str::beforeLast($status, '#') : $status,
                    'status_metadata' => $request->get('system_status')
                        ? $groupedStatuses->first()->status_metadata
                        : null,
                    'display_status' => $request->get('system_status')
                        ? $groupedStatuses->first()->display_status
                        : $status,
                    'start_date' => $groupedStatuses->first()->created_at,
                ];
            })
            ->values();

        for ($i = 0; $i < $statuses->count(); $i++) {
            $nextStatus = $statuses[$i + 1] ?? false;
            $endDate = $nextStatus ? $nextStatus['start_date'] : now();

            $statuses[$i] = [
                'status' => $statuses[$i]['status'],
                'display_status' => $statuses[$i]['display_status'],
                'status_metadata' => $statuses[$i]['status_metadata'],
                'start_date' => $statuses[$i]['start_date']->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString(),
                'diff_for_humans' => CarbonInterval::instance($statuses[$i]['start_date']->diff($endDate))->forHumans([
                    'parts' => 3,
                    'short' => true,
                ]),
            ];
        }

        return JsonResource::collection($statuses);
    }
}
