<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\OCRRequestStatus;
use App\Queries\OrdersListQuery;
use App\Http\Resources\OrdersJson;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\SideBySideOrder;
use Illuminate\Support\Facades\Storage;

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
        $companiesWithTemplates = Company::withTemplates();

        return new OrdersJson($orders, $companiesWithTemplates);
        // return OrdersJson::collection($orders);
    }

    /**
     * Display the specified resource.
     */
    public function show($orderId)
    {
        $order = $this->getBasicOrderForSideBySide($orderId);

        $this->authorize('view', $order);

        return new SideBySideOrder($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $orderData = $request->validate(Order::$rules);
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
        $order->updateRelatedModels($relatedModels);

        $order = $this->getBasicOrderForSideBySide($order->id);

        return new SideBySideOrder($order, false);
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->noContent();
    }

    protected function getBasicOrderForSideBySide($orderId)
    {
        return Order::query()
            ->select('t_orders.*')
            ->addSelect(['email_from_address' => DB::table('t_job_state_changes', 's_is')
                ->selectRaw("json_extract(s_is.status_metadata, '$.source_summary.source_email_from_address') as email_from_address")
                ->whereColumn('t_orders.request_id', 's_is.request_id')
                ->where('s_is.status', OCRRequestStatus::INTAKE_STARTED)
                ->limit(1)
            ])
            ->addSelect(['upload_user_name' => DB::table('t_job_state_changes', 's_ur')
                ->select('u.name')
                ->whereColumn('t_orders.request_id', 's_ur.request_id')
                ->where('s_ur.status', OCRRequestStatus::UPLOAD_REQUESTED)
                ->join('users as u', DB::raw("json_extract(s_ur.status_metadata, '$.user_id')"), '=', 'u.id')
                ->limit(1)
            ])
            ->addSelect(['submitted_date' => DB::table('t_job_state_changes', 'sub_date_state')
                ->select('sub_date_state.created_at')
                ->whereColumn('t_orders.request_id', 'sub_date_state.request_id')
                ->orderBy('created_at')
                ->limit(1)
            ])
            ->findOrFail($orderId);
    }
}
