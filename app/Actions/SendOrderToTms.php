<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\TMSProvider;
use App\Models\OCRRequestStatus;
use Illuminate\Validation\ValidationException;

class SendOrderToTms
{
    public function __invoke(Order $order)
    {
        $data = [
            'order_id' => $order->id,
            'status' => $this->getSubmitToTmsStatus($order->tmsProvider),
            'request_id' => $order->request_id,
            'company_id' => $order->t_company_id,
            'status_metadata' => [
                'tms_provider_id' => $order->t_tms_provider_id,
                'user_id' => auth()->id(),
                'company_id' => $order->t_company_id,
                'order_id' => $order->id,
            ]
        ];

        $response = app(PublishSnsMessageToUpdateStatus::class)($data);

        if ($response['status'] === 'ok') {
            $verifiableAttributes = collect(Order::$verifiableAttributes)
                ->map(fn ($value) => true)
                ->toArray();
            $order->update($verifiableAttributes);
        }

        return $response;
    }

    protected function getSubmitToTmsStatus(TMSProvider $tmsProvider): string
    {
        switch ($tmsProvider->name) {
            case TMSProvider::PROFIT_TOOLS:
                return OCRRequestStatus::SENDING_TO_WINT;
            case TMSProvider::COMPCARE:
                return OCRRequestStatus::SENDING_TO_COMPCARE;
            case TMSProvider::CARGOWISE:
                return OCRRequestStatus::SENDING_TO_CHAINIO;
        }
    }

    // protected function checkIfOrderIsValidated(Order $order)
    // {
    //     throw_if(! $order->isValidated(), ValidationException::withMessages(
    //         collect($order->notValidatedAddresses())->mapWithKeys(function ($attribute) {
    //             return [$attribute => 'The address is not validated'];
    //         })->toArray()
    //     ));
    // }
}
