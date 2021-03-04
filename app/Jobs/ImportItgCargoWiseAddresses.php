<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\CompanyAddressTMSCode;
use App\Traits\DeletesRemovedAddresses;

class ImportItgCargoWiseAddresses
{
    use DeletesRemovedAddresses;

    public bool $insertOnly;
    public Company $company;
    public TMSProvider $tmsProvider;

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

        $this->deleteAddressesRemovedInTheResponse(
            $this->addresses->pluck('org_code'),
            $this->company->id,
            $this->tmsProvider->id
        );

        $this->addresses
            ->when($this->insertOnly, function (Collection $addresses) {
                $existingCodes = CompanyAddressTMSCode::query()
                    ->forCompanyTmsProvider($this->company->id, $this->tmsProvider->id)
                    ->pluck('company_address_tms_code');
                return $addresses->whereNotIn('org_code', $existingCodes->toArray());
            })
            ->each(function ($address) {
                $address['is_billable'] = Str::of($address['receivable'])->upper()->exactly('Y');

                ImportItgCargoWiseAddress::dispatch($address, $this->tmsProvider->id, $this->company);
            });
    }

    public function setAddresses(Collection $addresses)
    {
        $this->addresses = $addresses;
    }
}
