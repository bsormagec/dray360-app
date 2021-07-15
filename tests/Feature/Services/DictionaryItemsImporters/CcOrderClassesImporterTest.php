<?php

namespace Tests\Feature\Services\DictionaryItemsImporters;

use Tests\TestCase;
use App\Models\Company;
use App\Models\DictionaryItem;
use Tests\Seeds\CompaniesSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\DictionaryItemsImporters\CcOrderClassesImporter;

class CcOrderClassesImporterTest extends TestCase
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
                        'OrderClassId' => 1,
                        'OrganizationId' => 4,
                        'OrderClassCode' => 'OC',
                        'OrderClass' => 'OC',
                        'OrderClassDescription' => 'OC',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-01-07T21:39:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ],
                    [
                        'OrderClassId' => 2,
                        'OrganizationId' => 19,
                        'OrderClassCode' => 'L',
                        'OrderClass' => 'L',
                        'OrderClassDescription' => 'L',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcOrderClassesImporter($this->company, [
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'item_key' => '1',
            'item_display_name' => 'OC',
            'item_value->OrderClassId' => 1,
            'item_value->OrderClassCode' => 'OC'
        ]);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'item_key' => '2',
            'item_display_name' => 'L',
            'item_value->OrderClassId' => 2,
            'item_value->OrderClassCode' => 'L'
        ]);
    }

    /** @test */
    public function it_updates_dictionary_items_if_it_already_exists()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'item_key' => '1',
            'item_display_name' => 'OC',
            'item_value' => [
                'OrderClassId' => 1,
                'OrganizationId' => 4,
                'OrderClassCode' => 'OC',
                'OrderClass' => 'OC',
                'OrderClassDescription' => 'OC',
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
                        'OrderClassId' => 1,
                        'OrganizationId' => 4,
                        'OrderClassCode' => 'OC UPDATED',
                        'OrderClass' => 'OC',
                        'OrderClassDescription' => 'OC',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-01-07T21:39:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ],
                ],
            ]);


        $importer = new CcOrderClassesImporter($this->company, [
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 1);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'item_key' => '1',
            'item_display_name' => 'OC UPDATED',
            'item_value->OrderClassId' => 1,
            'item_value->OrderClassCode' => 'OC UPDATED'
        ]);
    }

    /** @test */
    public function it_deletes_the_dictionary_items_that_were_not_found_in_the_returning_from_the_api()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'item_key' => '1',
            'item_display_name' => 'OC',
            'item_value' => [
                'OrderClassId' => 1,
                'OrganizationId' => 4,
                'OrderClassCode' => 'OC',
                'OrderClass' => 'OC',
                'OrderClassDescription' => 'OC',
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
                        'OrderClassId' => 2,
                        'OrganizationId' => 19,
                        'OrderClassCode' => 'L',
                        'OrderClass' => 'L',
                        'OrderClassDescription' => 'L',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcOrderClassesImporter($this->company, [
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'delete_missing' => true,
        ]);
        $importer->run();

        $this->assertSoftDeleted('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'item_key' => '1',
            'item_display_name' => 'OC',
            'item_value->OrderClassId' => 1,
            'item_value->OrderClassCode' => 'OC'
        ]);
    }

    /** @test */
    public function it_doesnt_delete_missing_values_from_the_endpoint()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'item_key' => '1',
            'item_display_name' => 'OC',
            'item_value' => [
                'OrderClassId' => 1,
                'OrganizationId' => 4,
                'OrderClassCode' => 'OC',
                'OrderClass' => 'OC',
                'OrderClassDescription' => 'OC',
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
                        'OrderClassId' => 2,
                        'OrganizationId' => 19,
                        'OrderClassCode' => 'L',
                        'OrderClass' => 'L',
                        'OrderClassDescription' => 'L',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcOrderClassesImporter($this->company, [
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_ORDERCLASS_TYPE,
            'item_key' => '1',
            'item_display_name' => 'OC',
            'item_value->OrderClassId' => 1,
            'item_value->OrderClassCode' => 'OC',
            'deleted_at' => null,
        ]);
    }
}
