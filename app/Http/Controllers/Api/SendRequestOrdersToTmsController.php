<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OCRRequest;
use Illuminate\Http\Response;
use App\Actions\SendOrderToTms;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;

class SendRequestOrdersToTmsController extends Controller
{
    const DONT_SEND_STATUSES = [
        OCRRequestStatus::SENDING_TO_WINT,
        OCRRequestStatus::AUTO_SENDING_TO_WINT,
        OCRRequestStatus::SUCCESS_SENDING_TO_WINT,
        OCRRequestStatus::SHIPMENT_CREATED_BY_WINT,
        OCRRequestStatus::UPDATING_TO_WINT,
        OCRRequestStatus::FAILURE_UPDATING_TO_WINT,
        OCRRequestStatus::SUCCESS_UPDATING_TO_WINT,
        OCRRequestStatus::SHIPMENT_UPDATED_BY_WINT,
        OCRRequestStatus::SHIPMENT_NOT_UPDATED_BY_WINT,

        OCRRequestStatus::SENDING_TO_CHAINIO,
        OCRRequestStatus::AUTO_SENDING_TO_CHAINIO,
        OCRRequestStatus::SUCCESS_SENDING_TO_CHAINIO,
        OCRRequestStatus::SHIPMENT_CREATED_BY_CHAINIO,

        OCRRequestStatus::UPDATED_BY_SUBSEQUENT_ORDER,
        OCRRequestStatus::SUCCESS_IMAGEUPLOADING_TO_BLACKFLY,
        OCRRequestStatus::FAILURE_IMAGEUPLOADING_TO_BLACKFLY,
        OCRRequestStatus::UNTRIED_IMAGEUPLOADING_TO_BLACKFLY,

        OCRRequestStatus::OCR_POST_PROCESSING_AUTOSUBMITED,
    ];

    public function __invoke($requestId)
    {
        $request = OCRRequest::where('request_id', $requestId)->firstOrFail();
        $this->authorize('sendToTms', $request);

        $responseTracker = [
            'sent' => 0,
            'not_sent' => 0,
            'failed' => 0,
            'messages' => [],
        ];

        $this->getOrders($requestId)->each(function ($order) use (&$responseTracker) {
            $status = $order->ocrRequest->latestOcrRequestStatus->status ?? null;

            $keyToUpdate = 'sent';
            $message = null;

            if (! $status || in_array($status, self::DONT_SEND_STATUSES)) {
                $keyToUpdate = 'not_sent';
                $message = "Ignored because status is: '{$status}'";
            } else {
                $response = app(SendOrderToTms::class)($order);
                $keyToUpdate = $response['status'] === 'error' ? 'failed' : 'sent';
                $message = $response['message'];
            }

            $responseTracker[$keyToUpdate]++;
            $responseTracker['messages'][] = [
                'order_id' => $order->id,
                'message' => $message,
            ];
        });

        return response()->json(['data' => $responseTracker]);
    }

    protected function getOrders($requestId): Collection
    {
        return Order::query()
            ->select([
                'id',
                'port_ramp_of_destination_address_verified',
                'port_ramp_of_origin_address_verified',
                'bill_to_address_verified',
                'request_id',
                't_company_id',
                't_tms_provider_id',
            ])
            ->where('request_id', $requestId)
            ->with('orderAddressEvents:id,t_order_id,t_address_verified')
            ->with('ocrRequest.latestOcrRequestStatus', function ($query) {
                $query->select(['id', 'status', 'request_id']);
            })
            ->with('tmsProvider:id,name')
            ->get();
    }
}
