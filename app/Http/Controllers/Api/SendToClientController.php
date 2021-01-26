<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToSendToClient;
use App\Actions\PublishSnsMessageToFinishRequestReview;

class SendToClientController extends Controller
{
    public function __invoke($orderId)
    {
        $order = $this->getOrder($orderId);
        $this->authorize('review', $order);
        $baseData = [
            'request_id' => $order->request_id,
            'company_id' => $order->t_company_id,
        ];

        if ($order->isTheLastUnderReview() && ! OCRRequestStatus::alreadyCompleted($order->request_id)) {
            $data = $baseData + [
                'status_metadata' => $order->getPostProcessingReviewStatusMetadata(),
            ];
            app(PublishSnsMessageToFinishRequestReview::class)($data);
        }

        $data = $baseData + [
            'order_id' => $orderId,
            'status_metadata' => $this->getReviewStatusMetadata($order),
        ];
        $response = app(PublishSnsMessageToSendToClient::class)($data);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
    }

    protected function getReviewStatusMetadata(Order $order)
    {
        $latestOcrRequestStatus = $order->ocrRequest->latestOcrRequestStatus ?? null;

        if ($latestOcrRequestStatus && $latestOcrRequestStatus->status == OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_REVIEW) {
            return $latestOcrRequestStatus->status_metadata;
        }

        $status = OCRRequestStatus::where([
            'order_id' => $order->id,
            'status' => OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_REVIEW,
        ])->first(['status', 'status_metadata']);

        return $status->status_metadata ?? [];
    }

    protected function getOrder($orderId): Order
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
            ->with('ocrRequest.latestOcrRequestStatus')
            ->find($orderId);
    }
}
