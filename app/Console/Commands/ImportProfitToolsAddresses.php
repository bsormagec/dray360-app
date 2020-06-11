<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use App\Services\Apis\RipCms;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use App\Models\CompanyAddressTMSCode;
use App\Jobs\ImportProfitToolsAddress;

class ImportProfitToolsAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:profit-tools-addresses {--insert-only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all the addresses from ripcms for Profit Tools and Cushing';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ripCmsApi = (new RipCms())->getToken();
        $this->info('Getting all the information from RipCms');
        $companiesAddress = collect($ripCmsApi->getCompanies());
        $company = Company::getCushing();
        $tmsProvider = TMSProvider::getProfitTools();

        $this->deleteAddressesRemovedInTheResponse($companiesAddress, $company, $tmsProvider);

        $this->info('Queueing the individual imports');
        $companiesAddress
            ->when($this->option('insert-only'), function (Collection $companies) {
                $existingCodes = CompanyAddressTMSCode::pluck('company_address_tms_code');
                return $companies->whereNotIn('id', $existingCodes->toArray());
            })
            ->each(function ($address) use ($company, $tmsProvider) {
                ImportProfitToolsAddress::dispatch($address, $company, $tmsProvider);
            });
    }

    protected function deleteAddressesRemovedInTheResponse(Collection $companiesAddress, $company, $tmsProvider)
    {
        Address::whereIn('id', function ($query) use ($companiesAddress, $company, $tmsProvider) {
            $query->select('t_address_id')
                ->from('t_company_address_tms_code')
                ->whereNotIn('company_address_tms_code', $companiesAddress->pluck('id'))
                ->where([
                    't_company_id' => $company->id,
                    't_tms_provider_id' => $tmsProvider->id,
                ]);
        })->delete();
        CompanyAddressTMSCode::query()
            ->whereNotIn('company_address_tms_code', $companiesAddress->pluck('id'))
            ->where([
                't_company_id' => $company->id,
                't_tms_provider_id' => $tmsProvider->id,
            ])
            ->delete();
    }
}
