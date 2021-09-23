<?php

namespace App\Console\Commands\Imports;

use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

abstract class ImportAddressesBaseCommand extends Command
{
    protected TMSProvider $tmsProvider;

    abstract protected function getTmsProvider(): TMSProvider;

    abstract protected function handleCompanyImport(Company $company);

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->tmsProvider = $this->getTmsProvider();

        $this->getCompaniesToImport()
            ->each(function ($company) {
                $this->handleCompanyImport($company);
            });
    }

    protected function getCompaniesToImport(): Collection
    {
        return Company::query()
            ->active()
            ->where([
                'default_tms_provider_id' => $this->tmsProvider->id,
                'sync_addresses' => true,
            ])
            ->when($this->option('company-name'), function ($query) {
                return $query->where('name', $this->option('company-name'));
            })
            ->when($this->option('company-id'), function ($query) {
                return $query->where('id', $this->option('company-id'));
            })
            ->get();
    }
}
