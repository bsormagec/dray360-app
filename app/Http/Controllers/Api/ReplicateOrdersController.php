<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\Order;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToUpdateStatus;

class ReplicateOrdersController extends Controller
{
    public function __invoke(Order $order)
    {
        $this->authorize('replicate', $order);
        $order->load(['orderAddressEvents', 'orderLineItems']);

        DB::beginTransaction();

        try {
            $newOrder = $this->replicateOrder($order);
            $this->replicateOrderAddressEvents($order, $newOrder);
            $this->replicateOrderLineItems($order, $newOrder);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['data' => $e->getTraceAsString()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = $this->submitReplicatedStatusToSns($order, $newOrder);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        sleep(3); //asdf This is a temporary solution while we implement the FIFO sns topic

        $response = $this->submitOrderInReviewStatusToSns($newOrder);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
    }

    protected function replicateOrder(Order $order): Order
    {
        $newOrder = $order->replicate([
            'tms_shipment_id',
            'unit_number',
            'tms_submission_datetime',
            'tms_cancelled_datetime',
            'seal_number',
        ]);

        return tap($newOrder)->save();
    }

    protected function replicateOrderAddressEvents(Order $order, Order $newOrder): void
    {
        $order->orderAddressEvents->each(function ($orderAddressEvent) use ($newOrder) {
            $newModel = $orderAddressEvent->replicate(['t_order_id']);
            $newModel->t_order_id = $newOrder->id;
            $newModel->save();
        });
    }

    protected function replicateOrderLineItems(Order $order, Order $newOrder): void
    {
        $order->orderLineItems->each(function ($orderLineItem) use ($newOrder) {
            $newModel = $orderLineItem->replicate(['t_order_id']);
            $newModel->t_order_id = $newOrder->id;
            $newModel->save();
        });
    }

    protected function submitReplicatedStatusToSns(Order $oldOrder, Order $newOrder): array
    {
        $data = [
            'request_id' => $newOrder->request_id,
            'company_id' => $newOrder->t_company_id,
            'order_id' => $newOrder->id,
            'status' => OCRRequestStatus::REPLICATED_FROM_EXISTING_ORDER,
            'status_metadata' => [
                'source_order_id' => $oldOrder->id,
                'user_id' => auth()->id(),
            ],
        ];
        return app(PublishSnsMessageToUpdateStatus::class)($data);
    }

    protected function submitOrderInReviewStatusToSns(Order $newOrder): array
    {
        $ocrRequestStatus = OCRRequestStatus::query()
            ->where('request_id', $newOrder->request_id)
            ->whereIn('status', [
                OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_REVIEW,
                OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_COMPLETE,
            ])
            ->first(['status_metadata']);

        $data = [
            'request_id' => $newOrder->request_id,
            'company_id' => $newOrder->t_company_id,
            'order_id' => $newOrder->id,
            'status' => OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_REVIEW,
            'status_metadata' => array_merge(
                ($ocrRequestStatus->status_metadata ?? []),
                ['user_id' => auth()->id()]
            ),
        ];
        return app(PublishSnsMessageToUpdateStatus::class)($data);
    }
}
