<?php

namespace Tests\Feature\Services\DictionaryItemsImporters;

use Tests\TestCase;
use App\Models\Company;
use App\Models\DictionaryItem;
use Tests\Seeds\CompaniesSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\DictionaryItemsImporters\CcCarriersImporter;

class CcCarriersImporterTest extends TestCase
{
    use DatabaseTransactions;

    protected Company $company;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CompaniesSeeder::class);
        $this->company = CompaniesSeeder::getTestCushing();
        $this->company->update(['compcare_api_key' => 'something']);
    }

    /** @test */
    public function it_creates_the_load_types_from_the_endpoint()
    {
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => ['token' => 'test1']
            ])
            ->push([
                'success' => true,
                'data' => [
                    [
                        'CarrierId' => 1,
                        'OrganizationId' => 4,
                        'CarrierScac' => 'proide',
                        'Carrier' => 'proide',
                        'CarrierName' => 'proide',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-01-07T21:39:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ],
                    [
                        'CarrierId' => 2,
                        'OrganizationId' => 19,
                        'CarrierScac' => 'tlkn',
                        'Carrier' => 'tlkn',
                        'CarrierName' => 'tlkn',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcCarriersImporter($this->company, [
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'item_key' => 'proide',
            'item_display_name' => 'proide',
            'item_value->CarrierScac' => 'proide',
            'item_value->CarrierName' => 'proide'
        ]);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'item_key' => 'tlkn',
            'item_display_name' => 'tlkn',
            'item_value->CarrierScac' => 'tlkn',
            'item_value->CarrierName' => 'tlkn'
        ]);
    }

    /** @test */
    public function it_updates_dictionary_items_if_it_already_exists()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'item_key' => 'proide',
            'item_display_name' => 'proide',
            'item_value' => [
                'CarrierId' => 1,
                'OrganizationId' => 4,
                'CarrierScac' => 'proide',
                'Carrier' => 'proide',
                'CarrierName' => 'proide',
                'InsertedUserId' => 0,
                'InsertedDate' => '2021-01-07T21:39:00',
                'UpdatedUserId' => null,
                'UpdatedDate' => null
            ],
        ]);
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => ['token' => 'test1']
            ])
            ->push([
                'success' => true,
                'data' => [
                    [
                        'CarrierId' => 1,
                        'OrganizationId' => 4,
                        'CarrierScac' => 'proide',
                        'Carrier' => 'proide',
                        'CarrierName' => 'proide UPDATED',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-01-07T21:39:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ],
                ],
            ]);


        $importer = new CcCarriersImporter($this->company, [
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 1);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'item_key' => 'proide',
            'item_display_name' => 'proide UPDATED',
            'item_value->CarrierScac' => 'proide',
            'item_value->CarrierName' => 'proide UPDATED'
        ]);
    }

    /** @test */
    public function it_deletes_the_dictionary_items_that_were_not_found_in_the_returning_from_the_api()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'item_key' => 'proide',
            'item_display_name' => 'proide',
            'item_value' => [
                'CarrierId' => 1,
                'OrganizationId' => 4,
                'CarrierScac' => 'proide',
                'Carrier' => 'proide',
                'CarrierName' => 'proide',
                'InsertedUserId' => 0,
                'InsertedDate' => '2021-01-07T21:39:00',
                'UpdatedUserId' => null,
                'UpdatedDate' => null
            ],
        ]);
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => ['token' => 'test1']
            ])
            ->push([
                'success' => true,
                'data' => [
                    [
                        'CarrierId' => 2,
                        'OrganizationId' => 19,
                        'CarrierScac' => 'tlkn',
                        'Carrier' => 'tlkn',
                        'CarrierName' => 'tlkn',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcCarriersImporter($this->company, [
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'delete_missing' => true,
        ]);
        $importer->run();

        $this->assertSoftDeleted('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'item_key' => 'proide',
            'item_display_name' => 'proide',
            'item_value->CarrierScac' => 'proide',
            'item_value->CarrierName' => 'proide'
        ]);
    }

    /** @test */
    public function it_doesnt_delete_missing_values_from_the_endpoint()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'item_key' => 'proide',
            'item_display_name' => 'proide',
            'item_value' => [
                'CarrierId' => 1,
                'OrganizationId' => 4,
                'CarrierScac' => 'proide',
                'Carrier' => 'proide',
                'CarrierName' => 'proide',
                'InsertedUserId' => 0,
                'InsertedDate' => '2021-01-07T21:39:00',
                'UpdatedUserId' => null,
                'UpdatedDate' => null
            ],
        ]);
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => ['token' => 'test1']
            ])
            ->push([
                'success' => true,
                'data' => [
                    [
                        'CarrierId' => 2,
                        'OrganizationId' => 19,
                        'CarrierScac' => 'tlkn',
                        'Carrier' => 'tlkn',
                        'CarrierName' => 'tlkn',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcCarriersImporter($this->company, [
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CARRIER_TYPE,
            'item_key' => 'proide',
            'item_display_name' => 'proide',
            'item_value->CarrierScac' => 'proide',
            'item_value->CarrierName' => 'proide',
            'deleted_at' => null,
        ]);
    }
}
