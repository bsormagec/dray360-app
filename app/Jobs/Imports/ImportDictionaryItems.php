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
use App\Services\DictionaryItemsImporters\CcLoadTypesImporter;
use App\Services\DictionaryItemsImporters\CcHaulClassesImporter;
use App\Services\DictionaryItemsImporters\CcOrderClassesImporter;
use App\Services\DictionaryItemsImporters\CcOrderStatusesImporter;
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
        $company = Company::find($this->companyId);
        $customHandler = $this->syncSettings['alternate_handler'] ?? null;
        $defaultHandler = $this->getDefaultImporter();

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

    protected function getDefaultImporter(): ?string
    {
        $defaultImporters = [
            DictionaryItem::TEMPLATE_TYPE => TemplatesImporter::class,
            DictionaryItem::CC_LOADTYPE_TYPE => CcLoadTypesImporter::class,
            DictionaryItem::CC_ORDERSTATUS_TYPE => CcOrderStatusesImporter::class,
            DictionaryItem::CC_HAULCLASS_TYPE => CcHaulClassesImporter::class,
            DictionaryItem::CC_ORDERCLASS_TYPE => CcOrderClassesImporter::class,
        ];

        return $defaultImporters[$this->itemType] ?? null;
    }
}
