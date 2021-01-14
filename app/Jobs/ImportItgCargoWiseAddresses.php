<?php

namespace App\Jobs;

use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\CompanyAddressTMSCode;

class ImportItgCargoWiseAddresses
{
    public bool $insertOnly;
    public Company $company;
    public TMSProvider $tmsProvider;

    protected Collection $billToCodes;
    protected Collection $addresses;
    protected $logger;

    public function __construct(Company $company, TMSProvider $tmsProvider, bool $insertOnly = false)
    {
        $this->insertOnly = $insertOnly;
        $this->company = $company;
        $this->companyName = $company->name;
        $this->tmsProvider = $tmsProvider;
    }

    public function handle()
    {
        if ($this->addresses->count() == 0) {
            Log::channel('imports')
                ->info("ImportItgCargoWiseAddress-{$this->company->name}: Aborting addresses import because addresses list is empty");
            return;
        }

        Log::channel('imports')
            ->info("ImportItgCargoWiseAddress-{$this->company->name}: Endpoint returned ".$this->addresses->count().' addresses');

        $this->deleteAddressesRemovedInTheResponse();

        $this->addresses
            ->when($this->insertOnly, function (Collection $addresses) {
                $existingCodes = CompanyAddressTMSCode::query()
                    ->forCompanyTmsProvider($this->company->id, $this->tmsProvider->id)
                    ->pluck('company_address_tms_code');
                return $addresses->whereNotIn('code', $existingCodes->toArray());
            })
            ->each(function ($address) {
                $address['is_billable'] = $this->billToCodes->contains($address['code']);

                ImportItgCargoWiseAddress::dispatch($address, $this->tmsProvider->id, $this->company);
            });
    }

    protected function deleteAddressesRemovedInTheResponse(): void
    {
        Address::whereIn('id', function ($query) {
            $query->select('t_address_id')
                ->from('t_company_address_tms_code')
                ->whereNotIn('company_address_tms_code', $this->addresses->pluck('code'))
                ->where([
                    't_company_id' => $this->company->id,
                    't_tms_provider_id' => $this->tmsProvider->id,
                ]);
        })->delete();
        CompanyAddressTMSCode::query()
            ->whereNotIn('company_address_tms_code', $this->addresses->pluck('code'))
            ->where([
                't_company_id' => $this->company->id,
                't_tms_provider_id' => $this->tmsProvider->id,
            ])
            ->delete();
    }

    public function setBillToAddresses(Collection $billToCodes)
    {
        $this->billToCodes = $billToCodes;
    }

    public function setAddresses(Collection $addresses)
    {
        $this->addresses = $addresses;
    }
}
