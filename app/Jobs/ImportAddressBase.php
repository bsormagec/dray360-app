<?php

namespace App\Jobs;

use App\Models\Address;
use App\Models\CompanyAddressTMSCode;

abstract class ImportAddressBase
{
    public int $companyId;
    public string $companyName;
    public int $tmsProviderId;
    public $addressCode;
    public $address;

    abstract protected function getAddressData();

    abstract protected function getTmsProviderCode(): string;

    public function handle()
    {
        $this->address = $this->getAddressData();
        $companyAddressTmsProvider = CompanyAddressTMSCode::query()
            ->forCompanyTmsProvider($this->companyId, $this->tmsProviderId)
            ->where('company_address_tms_code', $this->addressCode)
            ->with('address')
            ->first();

        if ($companyAddressTmsProvider) {
            $this->updateAddress($companyAddressTmsProvider->address, $this->address);
            return;
        }

        CompanyAddressTMSCode::createFrom(
            $this->addressCode,
            Address::createFrom($this->address, $this->getTmsProviderCode()),
            $this->companyId,
            $this->tmsProviderId
        );
    }

    protected function updateAddress(Address $oldAddress, $newData)
    {
        if ($oldAddress->isTheSameAs($newData, $this->getTmsProviderCode())) {
            return;
        }

        $oldAddress->updateFrom($newData, $this->getTmsProviderCode());
    }
}
