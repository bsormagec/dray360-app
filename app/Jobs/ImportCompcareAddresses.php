<?php

namespace App\Jobs;

use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Str;
use App\Services\Apis\Compcare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\CompanyAddressTMSCode;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportCompcareAddresses implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;

    public $queue = 'imports';
    public $tries = 5;
    public $timeout = 120;
    public $maxExceptions = 3;

    public bool $insertOnly;
    public int $companyId;
    public string $companyName;
    public int $tmsProviderId;
    protected $logger;

    public function __construct(Company $company, TMSProvider $tmsProvider, bool $insertOnly = false)
    {
        $this->insertOnly = $insertOnly;
        $this->companyId = $company->id;
        $this->companyName = $company->name;
        $this->tmsProviderId = $tmsProvider->id;
    }

    public function handle()
    {
        $company = Company::find($this->companyId);
        $compcareApi = (new Compcare($company))->getToken();
        $addresses = collect($compcareApi->getAllAddresses());

        if ($addresses->count() == 0) {
            Log::channel('imports')
                ->info("ImportCompcareAddresses-{$company->name}: Aborting addresses import because addresses list is empty");
            return;
        }

        Log::channel('imports')
            ->info("ImportCompcareAddresses-{$company->name}: Endpoint returned ".$addresses->count().' addresses');

        $this->deleteAddressesRemovedInTheResponse($addresses);

        $addresses
            ->when($this->insertOnly, function (Collection $companies) {
                $existingCodes = CompanyAddressTMSCode::query()
                    ->forCompanyTmsProvider($this->companyId, $this->tmsProviderId)
                    ->pluck('company_address_tms_code');
                return $companies->whereNotIn('AddressId', $existingCodes->toArray());
            })
            ->each(function ($address) use ($company) {
                ImportCompcareAddress::dispatch($address, $this->tmsProviderId, $company);
            });
    }

    protected function deleteAddressesRemovedInTheResponse(Collection $addresses): void
    {
        Address::whereIn('id', function ($query) use ($addresses) {
            $query->select('t_address_id')
                ->from('t_company_address_tms_code')
                ->whereNotIn('company_address_tms_code', $addresses->pluck('AddressId'))
                ->where([
                    't_company_id' => $this->companyId,
                    't_tms_provider_id' => $this->tmsProviderId,
                ]);
        })->delete();
        CompanyAddressTMSCode::query()
            ->whereNotIn('company_address_tms_code', $addresses->pluck('AddressId'))
            ->where([
                't_company_id' => $this->companyId,
                't_tms_provider_id' => $this->tmsProviderId,
            ])
            ->delete();
    }

    public function tags(): array
    {
        return [
            'import:compcare-addresses',
            'import-address-'.Str::snake($this->companyName),
        ];
    }
}
