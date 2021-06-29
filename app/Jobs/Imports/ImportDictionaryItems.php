<?php

namespace App\Jobs\Imports;

use Exception;
use App\Models\Company;
use App\Models\DictionaryItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\DictionaryItemsImporters\TemplatesImporter;
use App\Services\DictionaryItemsImporters\DictionaryItemsImporter;

class ImportDictionaryItems implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;

    public $queue = 'imports';
    public $tries = 5;
    public $timeout = 30;
    public $maxExceptions = 3;

    public int $companyId;
    public array $syncSettings;
    public string $itemType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Company $company, array $syncSettings)
    {
        $this->companyId = $company->id;
        unset($syncSettings['company']);
        $this->syncSettings = $syncSettings;
        $this->itemType = $syncSettings['item_type'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $importer = $this->getImporter();
        $importer->run();
    }

    protected function getImporter(): DictionaryItemsImporter
    {
        $defaultImporters = [
            DictionaryItem::TEMPLATE_TYPE => $this->defaultTemplatesImporter(),
        ];

        $company = Company::find($this->companyId);
        $customHandler = $this->syncSettings['alternate_handler'] ?? null;
        $defaultHandler = $defaultImporters[$this->itemType] ?? null;

        if ($customHandler) {
            throw_if(
                ! class_exists($customHandler),
                new Exception("The alternate handler class was not found [{$customHandler}]")
            );
            Log::channel('imports')->info("Using [{$customHandler}] for company: {$company->name}");

            return new $customHandler($company, $this->syncSettings);
        }

        throw_if(
            ! $defaultHandler,
            new Exception("The default handler class was not found for [{$this->itemType}]")
        );

        Log::channel('imports')->info("Using [{$defaultHandler}] for company: {$company->name}");
        return new $defaultHandler($company, $this->syncSettings);
    }

    protected function defaultTemplatesImporter(): string
    {
        return TemplatesImporter::class;
    }
}
