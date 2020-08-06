<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToSendToTms;
use Illuminate\Validation\ValidationException;

class SendToTmsController extends Controller
{
    const VALID_STATUSES = ['sending-to-wint'];

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(self::VALID_STATUSES)],
            'order_id' => 'required|integer|exists:t_orders,id',
        ]);
        $order = $this->getOrder($data['order_id']);
        $this->authorize('sendToTms', $order);

        // Do not validate that addresses are all verified=true, for now (July 7th, 2020, PBN)
        // $this->checkIfOrderIsValidated($order);

        $data['request_id'] = $order->request_id;
        $data['company_id'] = $order->t_company_id;
        $data['tms_provider_id'] = $order->t_tms_provider_id;
        $response = app(PublishSnsMessageToSendToTms::class)($data);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
    }

    protected function checkIfOrderIsValidated(Order $order)
    {
        throw_if(! $order->isValidated(), ValidationException::withMessages(
            collect($order->notValidatedAddresses())->mapWithKeys(function ($attribute) {
                return [$attribute => 'The address is not validated'];
            })->toArray()
        ));
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
            ->find($orderId);
    }
}
