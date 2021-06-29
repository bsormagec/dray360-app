<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\Company;
use App\Models\DictionaryItem;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Imports\ImportDictionaryItems;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportDictionaryItemsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_queue_jobs_for_each_company_dicitionary_item_type_pair_according_to_the_config()
    {
        Queue::fake();
        $company = factory(Company::class)->create([
            'configuration' => [
                'dictionary_items_sync' => [
                    [
                        'item_type' => DictionaryItem::TEMPLATE_TYPE,
                        'delete_missing' => false,
                    ],
                    [
                        'item_type' => DictionaryItem::VESSEL_TYPE,
                        'disabled' => true,
                        'delete_missing' => true,
                    ],
                ],
            ],
        ]);
        factory(Company::class)->create();

        $this->artisan('import:dictionary-items')->assertExitCode(0);

        Queue::assertPushed(ImportDictionaryItems::class, 1);
        Queue::assertPushed(ImportDictionaryItems::class, function ($job) use ($company) {
            return $job->companyId = $company->id
                && $job->itemType = DictionaryItem::TEMPLATE_TYPE;
        });
    }

    /** @test */
    public function it_filters_out_by_company()
    {
        Queue::fake();
        $companies = factory(Company::class, 2)->create([
            'configuration' => [
                'dictionary_items_sync' => [
                    [
                        'item_type' => DictionaryItem::TEMPLATE_TYPE,
                        'delete_missing' => false,
                    ],
                ],
            ],
        ]);
        factory(Company::class)->create();

        $this->artisan('import:dictionary-items', [
            '--company-id' => $companies->first()->id
        ])->assertExitCode(0);

        Queue::assertPushed(ImportDictionaryItems::class, 1);
        Queue::assertPushed(ImportDictionaryItems::class, function ($job) use ($companies) {
            return $job->companyId = $companies->first()->id
                && $job->itemType = DictionaryItem::TEMPLATE_TYPE;
        });
    }

    /** @test */
    public function it_filters_out_by_item_type()
    {
        Queue::fake();
        $companies = factory(Company::class)->create([
            'configuration' => [
                'dictionary_items_sync' => [
                    [
                        'item_type' => DictionaryItem::TEMPLATE_TYPE,
                        'delete_missing' => false,
                    ],
                    [
                        'item_type' => DictionaryItem::VESSEL_TYPE,
                        'delete_missing' => false,
                    ],
                    [
                        'item_type' => DictionaryItem::CARRIER_TYPE,
                        'delete_missing' => false,
                    ],
                ],
            ],
        ]);
        factory(Company::class)->create();

        $this->artisan('import:dictionary-items', [
            '--item-type' => DictionaryItem::VESSEL_TYPE
        ])->assertExitCode(0);

        Queue::assertPushed(ImportDictionaryItems::class, 1);
        Queue::assertPushed(ImportDictionaryItems::class, function ($job) use ($companies) {
            return $job->companyId = $companies->first()->id
                && $job->itemType = DictionaryItem::VESSEL_TYPE;
        });
    }
}
