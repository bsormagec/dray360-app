<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Http\Response;
use App\Models\DictionaryItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DictionaryItemsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginCustomerAdmin();
    }

    /** @test */
    public function it_should_return_the_list_of_dictionary_items_and_allow_filters()
    {
        $company = auth()->user()->company;
        factory(DictionaryItem::class, 5)->create(['t_company_id' => $company->id]);
        factory(DictionaryItem::class, 2)->create(['t_company_id' => $company->id, 'item_type' => 'another']);
        factory(DictionaryItem::class, 5)->create(['t_company_id' => factory(Company::class)->create()->id]);

        $this->loginAdmin();
        $this->getJson(route('dictionary-items.index'))->assertJsonCount(12, 'data');

        $this->loginCustomerAdmin();
        $this->getJson(route('dictionary-items.index'))->assertStatus(Response::HTTP_FORBIDDEN);
        $this->getJson(route('dictionary-items.index', [
            'filter[company_id]' => $company->id
        ]))->assertJsonCount(7, 'data');
        $this->getJson(route('dictionary-items.index', [
            'filter[company_id]' => $company->id,
            'filter[item_type]' => DictionaryItem::TEMPLATE_TYPE,
        ]))->assertJsonCount(5, 'data');
    }

    /** @test */
    public function it_should_allow_to_create_dictionary_items()
    {
        $company = auth()->user()->company;
        $this->loginAdmin();

        $this->postJson(route('dictionary-items.store'), [
                'item_key' => 'test',
                'item_display_name' => 'test',
                'item_type' => DictionaryItem::PT_IMAGETYPE_TYPE,
                't_company_id' => $company->id,
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'id',
                'item_key',
                'item_display_name',
            ]);

        $this->assertDatabaseCount('t_dictionary_items', 1);
    }

    /** @test */
    public function it_should_allow_to_create_dictionary_items_for_same_company_if_cant_see_other_companies()
    {
        $otherCompany = factory(Company::class)->create();

        $this->postJson(route('dictionary-items.store'), [
                'item_key' => 'test',
                'item_display_name' => 'test',
                'item_type' => DictionaryItem::PT_IMAGETYPE_TYPE,
                't_company_id' => $otherCompany->id,
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount('t_dictionary_items', 0);
    }
}
