<?php

namespace Tests\Feature\Services\DictionaryItemsImporters;

use Tests\TestCase;
use App\Models\Company;
use App\Models\DictionaryItem;
use Tests\Seeds\CompaniesSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\DictionaryItemsImporters\TemplatesImporter;

class TemplatesImporterTest extends TestCase
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
    public function it_creates_templates_from_the_blackfly_endpoint()
    {
        Http::fakeSequence()
            ->push([
                [
                    'ds_id' => 62207,
                    'ds_ref1_text' => 'GRIFFITH SPOTTING'
                ],
                [
                    'ds_id' => 62209,
                    'ds_ref1_text' => 'PEPSICO'
                ]
            ]);

        $importer = new TemplatesImporter($this->company, [
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => '62207',
            'item_display_name' => 'GRIFFITH SPOTTING',
            'item_value->ds_id' => 62207,
            'item_value->ds_ref1_text' => 'GRIFFITH SPOTTING'
        ]);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => '62209',
            'item_display_name' => 'PEPSICO',
            'item_value->ds_id' => 62209,
            'item_value->ds_ref1_text' => 'PEPSICO'
        ]);
    }

    /** @test */
    public function it_updates_dictionary_items_if_it_already_exists()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => 62207,
            'item_display_name' => 'GRIFFITH SPOTTING',
            'item_value' => [
                'ds_id' => 62207,
                'ds_ref1_text' => 'GRIFFITH SPOTTING',
            ],
        ]);
        Http::fakeSequence()
            ->push([
                [
                    'ds_id' => 62207,
                    'ds_ref1_text' => 'GRIFFITH EDITED'
                ]
            ]);


        $importer = new TemplatesImporter($this->company, [
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 1);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => '62207',
            'item_display_name' => 'GRIFFITH EDITED',
            'item_value->ds_id' => 62207,
            'item_value->ds_ref1_text' => 'GRIFFITH EDITED'
        ]);
    }

    /** @test */
    public function it_deletes_the_dictionary_items_that_were_not_found_in_the_returning_from_the_api()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => 62207,
            'item_display_name' => 'GRIFFITH SPOTTING',
            'item_value' => [
                'ds_id' => 62207,
                'ds_ref1_text' => 'GRIFFITH SPOTTING',
            ],
        ]);
        Http::fakeSequence()
            ->push([
                [
                    'ds_id' => 62209,
                    'ds_ref1_text' => 'PEPSICO'
                ]
            ]);


        $importer = new TemplatesImporter($this->company, [
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'delete_missing' => true,
        ]);
        $importer->run();

        $this->assertSoftDeleted('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => 62207,
            'item_display_name' => 'GRIFFITH SPOTTING',
            'item_value->ds_id' => 62207,
            'item_value->ds_ref1_text' => 'GRIFFITH SPOTTING'
        ]);
    }

    /** @test */
    public function it_doesnt_delete_missing_values_from_the_endpoint()
    {
        DictionaryItem::create([
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => 62207,
            'item_display_name' => 'GRIFFITH SPOTTING',
            'item_value' => [
                'ds_id' => 62207,
                'ds_ref1_text' => 'GRIFFITH SPOTTING',
            ],
        ]);
        Http::fakeSequence()
            ->push([
                [
                    'ds_id' => 62209,
                    'ds_ref1_text' => 'PEPSICO'
                ]
            ]);


        $importer = new TemplatesImporter($this->company, [
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'delete_missing' => false,
        ]);
        $importer->run();

        $this->assertDatabaseCount('t_dictionary_items', 2);
        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $this->company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => 62207,
            'item_display_name' => 'GRIFFITH SPOTTING',
            'item_value->ds_id' => 62207,
            'item_value->ds_ref1_text' => 'GRIFFITH SPOTTING',
            'deleted_at' => null,
        ]);
    }
}
