<?php

namespace Tests\Feature\Services\DictionaryItemsImporters;

use Tests\TestCase;
use App\Models\Company;
use App\Models\DictionaryItem;
use Tests\Seeds\CompaniesSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\DictionaryItemsImporters\PtEquipmentTypesImporter;

class PtEquipmentTypesImporterTest extends TestCase
{
    use DatabaseTransactions;

    protected Company $company;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CompaniesSeeder::class);
        $this->company = CompaniesSeeder::getTestCushing();
        $this->company->update(['blackfly_token' => 'something']);
    }

    /** @test */
    public function it_creates_equipments_from_the_blackfly_endpoint()
    {
        Http::fakeSequence()
            ->push([
                [
                    'id' => 539,
                    'line' => 'ANL SINGAPORE',
                    'type' => 'CH 20FT',
                    'equipmentlength' => '20FT',
                    'lineprefix' => null,
                    'scac' => 'ANLC',
                ],
                [
                    'id' => 540,
                    'line' => 'ANL SINGAPORE',
                    'type' => 'CH 40FT',
                    'equipmentlength' => '40FT',
                    'lineprefix' => null,
                    'scac' => 'ANLC',
                ],
            ]);

        $importer = new PtEquipmentTypesImporter($this->company, [
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'item_key' => '539',
            'item_display_name' => 'CH 20FT (ANL SINGAPORE)',
            'item_value->id' => 539,
            'item_value->type' => 'CH 20FT'
        ]);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'item_key' => '540',
            'item_display_name' => 'CH 40FT (ANL SINGAPORE)',
            'item_value->id' => 540,
            'item_value->type' => 'CH 40FT'
        ]);
    }

    /** @test */
    public function it_updates_dictionary_items_if_it_already_exists()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'item_key' => 539,
            'item_display_name' => 'CH 20FT (ANL SINGAPORE)',
            'item_value' => [
                'id' => 539,
                'line' => 'ANL SINGAPORE',
                'type' => 'CH 20FT',
                'equipmentlength' => '20FT',
                'lineprefix' => null,
                'scac' => 'ANLC',
            ],
        ]);
        Http::fakeSequence()
            ->push([
                [
                    'id' => 539,
                    'line' => 'ANL SINGAPORE UP',
                    'type' => 'CH 20FT',
                    'equipmentlength' => '20FT',
                    'lineprefix' => null,
                    'scac' => 'ANLC',
                ]
            ]);


        $importer = new PtEquipmentTypesImporter($this->company, [
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 1);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'item_key' => '539',
            'item_display_name' => 'CH 20FT (ANL SINGAPORE UP)',
            'item_value->id' => 539,
            'item_value->line' => 'ANL SINGAPORE UP'
        ]);
    }

    /** @test */
    public function it_deletes_the_dictionary_items_that_were_not_found_in_the_returning_from_the_api()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'item_key' => 539,
            'item_display_name' => 'CH 20FT (ANL SINGAPORE)',
            'item_value' => [
                'id' => 539,
                'line' => 'ANL SINGAPORE',
                'type' => 'CH 20FT',
                'equipmentlength' => '20FT',
                'lineprefix' => null,
                'scac' => 'ANLC',
            ],
        ]);
        Http::fakeSequence()
            ->push([
                [
                    'id' => 540,
                    'line' => 'ANL SINGAPORE',
                    'type' => 'CH 40FT',
                    'equipmentlength' => '40FT',
                    'lineprefix' => null,
                    'scac' => 'ANLC',
                ],
            ]);


        $importer = new PtEquipmentTypesImporter($this->company, [
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'delete_missing' => true,
        ]);
        $importer->run();

        $this->assertSoftDeleted('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'item_key' => 539,
            'item_display_name' => 'CH 20FT (ANL SINGAPORE)',
            'item_value->id' => 539,
            'item_value->line' => 'ANL SINGAPORE'
        ]);
    }

    /** @test */
    public function it_doesnt_delete_missing_values_from_the_endpoint()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'item_key' => 539,
            'item_display_name' => 'CH 20FT (ANL SINGAPORE)',
            'item_value' => [
                'id' => 539,
                'line' => 'ANL SINGAPORE',
                'type' => 'CH 20FT',
                'equipmentlength' => '20FT',
                'lineprefix' => null,
                'scac' => 'ANLC',
            ],
        ]);
        Http::fakeSequence()
            ->push([
                [
                    'id' => 540,
                    'line' => 'ANL SINGAPORE',
                    'type' => 'CH 40FT',
                    'equipmentlength' => '40FT',
                    'lineprefix' => null,
                    'scac' => 'ANLC',
                ],
            ]);


        $importer = new PtEquipmentTypesImporter($this->company, [
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::PT_EQUIPMENTTYPE_TYPE,
            'item_key' => 539,
            'item_display_name' => 'CH 20FT (ANL SINGAPORE)',
            'item_value->id' => 539,
            'item_value->line' => 'ANL SINGAPORE',
            'deleted_at' => null,
        ]);
    }
}
