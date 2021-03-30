<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\Address;
use App\Events\AddressVerified;
use App\Models\VerifiedAddress;
use App\Models\CompanyAddressTMSCode;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrUpdateVerifiedAddressEntry implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(AddressVerified $event)
    {
        $model = ($event->type)::find($event->data['id'] ?? null);

        if (! $model) {
            return;
        }

        $address = $this->getAddress($model);
        $baseData = $this->getBaseData($model, $address);

        $verifiedAddress = VerifiedAddress::firstOrCreate($baseData, [
            'verified_count' => 0,
            'skip_verification' => false,
            'company_address_tms_text' => $address->address_concatenated_text,
        ]);

        if ($verifiedAddress->wasRecentlyCreated) {
            return $verifiedAddress;
        }

        $verifiedAddress->verified_count++;
        $verifiedAddress->company_address_tms_text = $address->address_concatenated_text;

        if ($verifiedAddress->verified_count > currentCompany()->automatic_address_verification_threshold) {
            $verifiedAddress->skip_verification = true;
        }

        app('company_manager')->setCompany(null);

        return tap($verifiedAddress)->save();
    }

    protected function getAddress($model): Address
    {
        $addressIdKey = $model instanceof Order ? 'bill_to_address_id' : 't_address_id';

        return Address::find($model->{$addressIdKey}, ['id', 'address_concatenated_text']);
    }

    protected function getBaseData($model, Address $address): array
    {
        if ($model instanceof Order) {
            app('company_manager')->setCompanyFromId($model->t_company_id);

            $companyAddressTmsCode = CompanyAddressTMSCode::query()
                ->forCompanyTmsProvider($model->t_company_id, $model->t_tms_provider_id)
                ->where('t_address_id', $address->id)
                ->first(['company_address_tms_code']);

            return [
                't_company_id' => $model->t_company_id,
                't_tms_provider_id' => $model->t_tms_provider_id,
                'company_address_tms_code' => $companyAddressTmsCode->company_address_tms_code,
                'ocr_address_raw_text' => $model->bill_to_address_raw_text,
            ];
        }

        $order = Order::find($model->t_order_id, ['id', 't_company_id', 't_tms_provider_id']);
        $companyAddressTmsCode = CompanyAddressTMSCode::query()
            ->forCompanyTmsProvider($order->t_company_id, $order->t_tms_provider_id)
            ->where('t_address_id', $model->t_address_id)
            ->first(['company_address_tms_code']);

        app('company_manager')->setCompanyFromId($order->getCompanyId());

        return [
            't_company_id' => $order->t_company_id,
            't_tms_provider_id' => $order->t_tms_provider_id,
            'company_address_tms_code' => $companyAddressTmsCode->company_address_tms_code,
            'ocr_address_raw_text' => $model->t_address_raw_text,
        ];
    }
}
