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
        $address = $this->getAddress($event);
        $baseData = $this->getBaseData($event, $address);

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

    protected function getAddress(AddressVerified $event): Address
    {
        $addressIdKey = $event->type == Order::class ? 'bill_to_address_id' : 't_address_id';

        return Address::find($event->data[$addressIdKey], ['id', 'address_concatenated_text']);
    }

    protected function getBaseData(AddressVerified $event, Address $address): array
    {
        $data = $event->data;

        if ($event->type == Order::class) {
            app('company_manager')->setCompanyFromId($data['t_company_id']);

            $companyAddressTmsCode = CompanyAddressTMSCode::query()
                ->forCompanyTmsProvider($data['t_company_id'], $data['t_tms_provider_id'])
                ->where('t_address_id', $address->id)
                ->first(['company_address_tms_code']);

            return [
                't_company_id' => $data['t_company_id'],
                't_tms_provider_id' => $data['t_tms_provider_id'],
                'company_address_tms_code' => $companyAddressTmsCode->company_address_tms_code,
                'ocr_address_raw_text' => $data['bill_to_address_raw_text'],
            ];
        }

        $order = Order::find($data['t_order_id'], ['id', 't_company_id', 't_tms_provider_id']);
        $companyAddressTmsCode = CompanyAddressTMSCode::query()
            ->forCompanyTmsProvider($order->t_company_id, $order->t_tms_provider_id)
            ->where('t_address_id', $data['t_address_id'])
            ->first(['company_address_tms_code']);

        app('company_manager')->setCompanyFromId($order->getCompanyId());

        return [
            't_company_id' => $order->t_company_id,
            't_tms_provider_id' => $order->t_tms_provider_id,
            'company_address_tms_code' => $companyAddressTmsCode->company_address_tms_code,
            'ocr_address_raw_text' => $data['t_address_raw_text'],
        ];
    }
}
