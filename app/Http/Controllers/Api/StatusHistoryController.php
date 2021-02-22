<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusHistoryController extends Controller
{
    public function __invoke(Request $request)
    {
        $statuses = null;

        if ($request->get('request_id')) {
            $statuses = $this->getRequestStatusHistory($request);
        } elseif ($request->get('order_id')) {
            $statuses = $this->getOrderStatusHistory($request);
        } else {
            return response()->json(['data' => '`request_id` or `order_id` required'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $statuses = $statuses
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
                    'company_id' => $groupedStatuses->first()->company_id,
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
                'start_date' => $statuses[$i]['start_date']->toISOString(),
                'company_id' => $statuses[$i]['company_id'],
                'end_date' => $endDate->toISOString(),
                'diff_for_humans' => CarbonInterval::instance($statuses[$i]['start_date']->diff($endDate))->forHumans([
                    'parts' => 3,
                    'short' => true,
                ]),
            ];
        }

        return JsonResource::collection($statuses);
    }

    protected function getRequestStatusHistory(Request $request): Collection
    {
        return OCRRequestStatus::query()
            ->select(['id', 'created_at', 'status', 'status_date', 'company_id'])
            ->when($request->get('system_status'), function ($query) {
                return $query->addSelect('status_metadata');
            })
            ->whereNull('order_id')
            ->where('request_id', $request->get('request_id'))
            ->orderBy('id')
            ->get();
    }

    protected function getOrderStatusHistory(Request $request): Collection
    {
        $order = Order::find($request->get('order_id'));

        return OCRRequestStatus::query()
            ->select(['id', 'created_at', 'status', 'status_date', 'company_id'])
            ->when($request->get('system_status'), function ($query) {
                return $query->addSelect('status_metadata');
            })
            ->where(function ($query) use ($order) {
                $query->where('request_id', $order->request_id)
                    ->whereNull('order_id');
            })
            ->orWhere('order_id', $order->id)
            ->orderBy('id')
            ->get();
    }
}
