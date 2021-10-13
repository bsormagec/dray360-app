<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OCRRequestStatus;
use App\Queries\OrdersListQuery;
use App\Http\Resources\OrdersJson;
use App\Http\Controllers\Controller;
use App\Http\Resources\SideBySideOrder;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateOrderRequest;
use App\Actions\PublishSnsMessageToUpdateStatus;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);
        $perPage = $request->get('perPage', 25);

        $orders = (new OrdersListQuery())->paginate($perPage);

        return new OrdersJson($orders);
    }

    /**
     * Display the specified resource.
     */
    public function show($orderId)
    {
        $order = Order::getBasicOrderForSideBySide($orderId);

        $this->authorize('view', $order);

        return new SideBySideOrder($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $this->authorize('update', $order);
        $orderData = $request->validated();
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

        $order->update($orderData);
        $order->updateEquipmentTypesDictionaryItems($orderData);
        $order->updateRelatedModels($relatedModels);

        $order = Order::getBasicOrderForSideBySide($order->id);

        return new SideBySideOrder($order, false);
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        if ($order->isTheLastUnderReview() && ! OCRRequestStatus::alreadyCompleted($order->request_id)) {
            $data = [
                'request_id' => $order->request_id,
                'company_id' => $order->t_company_id,
                'status' => OCRRequestStatus::OCR_POST_PROCESSING_COMPLETE,
                'status_metadata' => array_merge(
                    $order->getPostProcessingReviewStatusMetadata(),
                    ['user_id' => auth()->id()]
                ),
            ];
            app(PublishSnsMessageToUpdateStatus::class)($data);
        }

        $order->delete();

        return response()->noContent();
    }
}
