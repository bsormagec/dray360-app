<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\TMSProvider;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToSendToTms;
use Illuminate\Validation\ValidationException;

class SendToTmsController extends Controller
{
    public function __invoke($orderId)
    {
        $order = $this->getOrder($orderId);
        $this->authorize('sendToTms', $order);
        $tmsProvider = $this->getTmsProvider($order->t_tms_provider_id);
        $data = [
            'order_id' => $orderId,
            'status' => $this->getSubmitToTmsStatus($tmsProvider),
        ];

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

    protected function getSubmitToTmsStatus($tmsProvider)
    {
        switch ($tmsProvider->name) {
            case TMSProvider::PROFIT_TOOLS:
                return OCRRequestStatus::SENDING_TO_WINT;
            case TMSProvider::COMPCARE:
                return OCRRequestStatus::SENDING_TO_CHAINIO;
        }
    }

    protected function checkIfOrderIsValidated(Order $order)
    {
        throw_if(! $order->isValidated(), ValidationException::withMessages(
            collect($order->notValidatedAddresses())->mapWithKeys(function ($attribute) {
                return [$attribute => 'The address is not validated'];
            })->toArray()
        ));
    }

    protected function getTmsProvider($tmsProviderId): TMSProvider
    {
        return TMSProvider::firstWhere('id', $tmsProviderId);
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
