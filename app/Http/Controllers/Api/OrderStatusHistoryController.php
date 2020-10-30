<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Carbon\CarbonInterval;
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
            ->where(function ($query) use ($order) {
                $query->where('request_id', $order->request_id)
                    ->whereNull('order_id');
            })
            ->orWhere('order_id', $order->id)
            ->orderBy('order_id')
            ->orderBy('id')
            ->get()
            ->groupBy($request->get('system_status') ? 'status' : 'display_status')
            ->map(function ($groupedStatuses, $status) {
                $groupedStatuses = $groupedStatuses->sortBy('id');
                return [
                    'status' => $status,
                    'start_date' => $groupedStatuses->first()->created_at,
                ];
            })
            ->values();

        for ($i = 0; $i < $statuses->count(); $i++) {
            $nextStatus = $statuses[$i + 1] ?? false;
            $endDate = $nextStatus ? $nextStatus['start_date'] : now();

            $statuses[$i] = [
                'status' => $statuses[$i]['status'],
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
