<?php

namespace App\Console\Commands\Imports;

use App\Models\Company;
use Illuminate\Console\Command;
use App\Jobs\Imports\ImportDictionaryItems as ImportDictionaryItemsJob;

class ImportDictionaryItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:dictionary-items {--company-id=} {--item-type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queues the jobs to import dictionary items';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Company::query()
            ->active()
            ->when($this->option('company-id'), fn ($query) => $query->where('id', $this->option('company-id')))
            ->whereJsonLength('configuration->dictionary_items_sync', '>', 0)
            ->get(['id', 'name', 'configuration'])
            ->flatMap(function ($company) {
                return collect($company->configuration['dictionary_items_sync'])
                    ->map(fn ($item) => $item + ['company' => $company])
                    ->toArray();
            })
            ->reject(fn ($itemPerCompany) => $itemPerCompany['disabled'] ?? false)
            ->when($this->option('item-type'), function ($collection) {
                return $collection->filter(fn ($item) => $item['item_type'] === $this->option('item-type'));
            })
            ->each(function ($itemPerCompany) {
                $this->info("Queueing import of '{$itemPerCompany['item_type']}' for " . $itemPerCompany['company']->name);
                ImportDictionaryItemsJob::dispatch($itemPerCompany['company'], $itemPerCompany);
            });
    }
}
