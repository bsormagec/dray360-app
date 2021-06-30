<?php

namespace App\Jobs\Imports;

use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Services\Apis\Compcare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\CompanyAddressTMSCode;
use App\Traits\DeletesRemovedAddresses;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportCompcareAddresses implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use DeletesRemovedAddresses;

    public $queue = 'imports';
    public $tries = 1;
    public $timeout = 600;
    public $maxExceptions = 1;

    public bool $insertOnly;
    public int $companyId;
    public string $companyName;
    public int $tmsProviderId;
    public int $page;
    public int $limit = 500;
    public array $apiAddresses;
    protected $logger;

    public function __construct(
        Company $company,
        TMSProvider $tmsProvider,
        bool $insertOnly = false,
        int $page = 1,
        array $apiAddresses = []
    ) {
        $this->insertOnly = $insertOnly;
        $this->companyId = $company->id;
        $this->companyName = $company->name;
        $this->tmsProviderId = $tmsProvider->id;
        $this->page = $page;
        $this->apiAddresses = $apiAddresses;
    }

    public function handle()
    {
        set_time_limit($this->timeout);
        $company = Company::find($this->companyId);
        $compcareApi = (new Compcare($company))->getToken();
        $response = $compcareApi->getAddresses($this->page, $this->limit);
        $responseIsEmpty = count($response['data']) === 0;

        if ($responseIsEmpty && count($this->apiAddresses) == 0) {
            Log::channel('imports')
                ->info("ImportCompcareAddresses-{$company->name}: Aborting addresses import because addresses list is empty");
            return;
        }

        if ($responseIsEmpty && count($this->apiAddresses) > 0) {
            Log::channel('imports')
                ->info("ImportCompcareAddresses-{$company->name}: Endpoint returned ".count($this->apiAddresses).' addresses');

            $this->deleteAddressesRemovedInTheResponse(
                collect($this->apiAddresses),
                $this->companyId,
                $this->tmsProviderId
            );
            return;
        }


        $addresses = collect($response['data'])
            ->reject(function ($address) {
                return strtoupper(Arr::get($address, 'LocationType.LocationTypeCode', '')) == 'S';
            });

        if ($addresses->count() != 0) {
            $this->apiAddresses = collect($this->apiAddresses)
                ->merge(collect($addresses)->pluck('EntityId'))
                ->toArray();
        }

        $addresses
            ->when($this->insertOnly, function (Collection $addresses) {
                $existingCodes = CompanyAddressTMSCode::query()
                    ->forCompanyTmsProvider($this->companyId, $this->tmsProviderId)
                    ->pluck('company_address_tms_code');
                return $addresses->whereNotIn('EntityId', $existingCodes->toArray());
            })
            ->each(function ($address) use ($company) {
                ImportCompcareAddress::dispatch($address, $this->tmsProviderId, $company);
            });

        self::dispatch(
            $company,
            TMSProvider::find($this->tmsProviderId),
            $this->insertOnly,
            ++$this->page,
            $this->apiAddresses
        );
    }

    public function tags(): array
    {
        return [
            'import:compcare-addresses',
            'import-address-'.Str::snake($this->companyName),
        ];
    }
}
