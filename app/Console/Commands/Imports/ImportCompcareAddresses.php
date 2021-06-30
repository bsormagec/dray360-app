<?php

namespace App\Console\Commands\Imports;

use App\Models\Company;
use App\Models\TMSProvider;
use App\Jobs\Imports\ImportCompcareAddresses as ImportCompcareAddressesJob;

class ImportCompcareAddresses extends ImportAddressesBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:compcare-addresses {--insert-only} {--company-name=} {--company-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all the addresses from Compcare';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    protected function handleCompanyImport(Company $company)
    {
        $this->info("Queueing import for {$company->name}");
        ImportCompcareAddressesJob::dispatch(
            $company,
            $this->tmsProvider,
            $this->option('insert-only')
        );
    }

    protected function getTmsProvider(): TMSProvider
    {
        return TMSProvider::getCompcare();
    }
}
