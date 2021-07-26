<?php

namespace Tests\Feature\Services\DictionaryItemsImporters;

use Tests\TestCase;
use App\Models\Company;
use App\Models\DictionaryItem;
use Tests\Seeds\CompaniesSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\DictionaryItemsImporters\CcContainerSizesImporter;

class CcContainerSizesImporterTest extends TestCase
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
                        'ContainerSizeId' => 1,
                        'OrganizationId' => 4,
                        'ContainerSizeCode' => '20',
                        'ContainerSize' => '20',
                        'ContainerSizeDescription' => '20',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-01-07T21:39:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ],
                    [
                        'ContainerSizeId' => 2,
                        'OrganizationId' => 19,
                        'ContainerSizeCode' => '40',
                        'ContainerSize' => '40',
                        'ContainerSizeDescription' => '40',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcContainerSizesImporter($this->company, [
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'item_key' => '20',
            'item_display_name' => '20',
            'item_value->ContainerSizeCode' => '20',
            'item_value->ContainerSizeDescription' => '20'
        ]);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'item_key' => '40',
            'item_display_name' => '40',
            'item_value->ContainerSizeCode' => '40',
            'item_value->ContainerSizeDescription' => '40'
        ]);
    }

    /** @test */
    public function it_updates_dictionary_items_if_it_already_exists()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'item_key' => '20',
            'item_display_name' => '20',
            'item_value' => [
                'ContainerSizeId' => 1,
                'OrganizationId' => 4,
                'ContainerSizeCode' => '20',
                'ContainerSize' => '20',
                'ContainerSizeDescription' => '20',
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
                        'ContainerSizeId' => 1,
                        'OrganizationId' => 4,
                        'ContainerSizeCode' => '20',
                        'ContainerSize' => '20',
                        'ContainerSizeDescription' => '20 UPDATED',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-01-07T21:39:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ],
                ],
            ]);


        $importer = new CcContainerSizesImporter($this->company, [
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 1);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'item_key' => '20',
            'item_display_name' => '20 UPDATED',
            'item_value->ContainerSizeCode' => '20',
            'item_value->ContainerSizeDescription' => '20 UPDATED'
        ]);
    }

    /** @test */
    public function it_deletes_the_dictionary_items_that_were_not_found_in_the_returning_from_the_api()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'item_key' => '20',
            'item_display_name' => '20',
            'item_value' => [
                'ContainerSizeId' => 1,
                'OrganizationId' => 4,
                'ContainerSizeCode' => '20',
                'ContainerSize' => '20',
                'ContainerSizeDescription' => '20',
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
                        'ContainerSizeId' => 2,
                        'OrganizationId' => 19,
                        'ContainerSizeCode' => '40',
                        'ContainerSize' => '40',
                        'ContainerSizeDescription' => '40',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcContainerSizesImporter($this->company, [
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'delete_missing' => true,
        ]);
        $importer->run();

        $this->assertSoftDeleted('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'item_key' => '20',
            'item_display_name' => '20',
            'item_value->ContainerSizeCode' => '20',
            'item_value->ContainerSizeDescription' => '20'
        ]);
    }

    /** @test */
    public function it_doesnt_delete_missing_values_from_the_endpoint()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'item_key' => '20',
            'item_display_name' => '20',
            'item_value' => [
                'ContainerSizeId' => 1,
                'OrganizationId' => 4,
                'ContainerSizeCode' => '20',
                'ContainerSize' => '20',
                'ContainerSizeDescription' => '20',
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
                        'ContainerSizeId' => 2,
                        'OrganizationId' => 19,
                        'ContainerSizeCode' => '40',
                        'ContainerSize' => '40',
                        'ContainerSizeDescription' => '40',
                        'InsertedUserId' => 0,
                        'InsertedDate' => '2021-03-11T18:47:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ],
            ]);

        $importer = new CcContainerSizesImporter($this->company, [
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::CC_CONTAINERSIZE_TYPE,
            'item_key' => '20',
            'item_display_name' => '20',
            'item_value->ContainerSizeCode' => '20',
            'item_value->ContainerSizeDescription' => '20',
            'deleted_at' => null,
        ]);
    }
}
