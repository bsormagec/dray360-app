<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Response;
use App\Actions\SendOrderToTms;
use App\Http\Controllers\Controller;

class SendToTmsController extends Controller
{
    public function __invoke($orderId)
    {
        $order = $this->getOrder($orderId);
        $this->authorize('sendToTms', $order);

        $response = app(SendOrderToTms::class)($order);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
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
            ->with('orderAddressEvents:id,t_order_id,t_address_verified')
            ->with('tmsProvider:id,name')
            ->find($orderId);
    }
}
