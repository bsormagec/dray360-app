<?php

namespace App\Console\Commands\Imports;

use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Str;
use App\Imports\ItgAddressesFileRead;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Imports\ImportItgCargoWiseAddresses;

class ImportCargoWiseAddresses extends ImportAddressesBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cargowise-addresses {--insert-only} {--company-name=} {--company-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all the addresses from CargoWise';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    protected function handleCompanyImport(Company $company)
    {
        set_time_limit(900);
        if (! Str::contains($company->name, ['itg', 'ITG'])) {
            $this->info("Queueing import for {$company->name}");
            return;
        }

        $this->info("Reading xlsx files for {$company->name}");
        $storageDriver = Storage::disk('s3-file-ingestion');
        $files = $storageDriver->allFiles("/company_{$company->id}/addressfiles");
        $fileToRead = collect($files)
            ->map(function ($file) use ($storageDriver) {
                return [
                    'path' => $file,
                    'last_modified' => $storageDriver->lastModified($file),
                ];
            })
            ->sortByDesc('last_modified')
            ->first();

        if (! $fileToRead) {
            $this->info("No files found for {$company->name}");
            return;
        }

        $this->info("Reading file: {$fileToRead['path']}");
        $job = new ImportItgCargoWiseAddresses(
            $company,
            $this->tmsProvider,
            $this->option('insert-only')
        );
        (new ItgAddressesFileRead($job))->import($fileToRead['path'], 's3-file-ingestion');

        $this->info("Queueing import for {$company->name}");
        $job->handle();
    }

    protected function getTmsProvider(): TMSProvider
    {
        return TMSProvider::getCargoWise();
    }
}
