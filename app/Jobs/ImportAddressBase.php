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
            $this->updateCompanyAddressTmsCode($companyAddressTmsProvider);
            return;
        }

        $this->createNewCompanyTmsCodeEntry();
    }

    protected function updateCompanyAddressTmsCode(CompanyAddressTMSCode $companyAddressTmsProvider)
    {
        if ($companyAddressTmsProvider->address->isTheSameAs($this->address, $this->getTmsProviderCode())) {
            return;
        }

        $companyAddressTmsProvider->delete();

        $this->createNewCompanyTmsCodeEntry();
    }

    protected function createNewCompanyTmsCodeEntry()
    {
        CompanyAddressTMSCode::createFrom(
            $this->addressCode,
            Address::createFrom($this->address, $this->getTmsProviderCode()),
            $this->companyId,
            $this->tmsProviderId
        );
    }
}
