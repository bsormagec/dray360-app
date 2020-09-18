<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use App\Services\Apis\RipCms;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use App\Exceptions\RipCmsException;
use Illuminate\Support\Facades\Log;
use App\Models\CompanyAddressTMSCode;
use App\Jobs\ImportProfitToolsAddress;

class ImportProfitToolsAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:profit-tools-addresses {--insert-only} {--company-name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all the addresses from ripcms for Profit Tools and Cushing';

    protected TMSProvider $tmsProvider;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->tmsProvider = TMSProvider::getProfitTools();

        $this->getCompaniesToImport()
            ->each(function ($company) {
                try {
                    $this->importAdressesFor($company);
                } catch (RipCmsException $e) {
                }
            });
    }

    protected function getCompaniesToImport(): Collection
    {
        return collect([
            Company::getCushing(),
            Company::getTCompaniesDev(),
            Company::getIXT(),
            Company::getIXTOnboarding()
        ])->when($this->option('company-name'), function ($collection) {
            return $collection->reject(fn ($company) => $company->name != $this->option('company-name'));
        });
    }

    protected function importAdressesFor(Company $company)
    {
        $ripCmsApi = (new RipCms($company))->getToken();
        $this->info("Getting all the information from RipCms for {$company->name}");
        $companiesAddress = collect($ripCmsApi->getCompanies());

        if ($companiesAddress->count() == 0) {
            Log::info('Aborting addresses import because addresses list is empty');
            return;
        }

        $this->deleteAddressesRemovedInTheResponse($companiesAddress, $company);

        $this->info("Queueing the individual imports for {$company->name}");
        $companiesAddress
            ->when($this->option('insert-only'), function (Collection $companies) use ($company) {
                $existingCodes = CompanyAddressTMSCode::query()
                    ->forCompanyTmsProvider($company->id, $this->tmsProvider->id)
                    ->pluck('company_address_tms_code');
                return $companies->whereNotIn('id', $existingCodes->toArray());
            })
            ->each(function ($address) use ($company) {
                ImportProfitToolsAddress::dispatch($address, $company, $this->tmsProvider);
            });
    }

    protected function deleteAddressesRemovedInTheResponse(Collection $companiesAddress, Company $company): void
    {
        Address::whereIn('id', function ($query) use ($companiesAddress, $company) {
            $query->select('t_address_id')
                ->from('t_company_address_tms_code')
                ->whereNotIn('company_address_tms_code', $companiesAddress->pluck('id'))
                ->where([
                    't_company_id' => $company->id,
                    't_tms_provider_id' => $this->tmsProvider->id,
                ]);
        })->delete();
        CompanyAddressTMSCode::query()
            ->whereNotIn('company_address_tms_code', $companiesAddress->pluck('id'))
            ->where([
                't_company_id' => $company->id,
                't_tms_provider_id' => $this->tmsProvider->id,
            ])
            ->delete();
    }
}
