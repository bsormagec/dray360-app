<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Http\Response;
use App\Models\DictionaryItem;
use App\Models\OrderAddressEvent;
use Tests\Seeds\OrdersTableSeeder;
use App\Models\DictionaryCacheEntry;
use App\Models\DictionaryCacheDefinition;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateOrUpdateCacheEntryForTmsTemplateTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(OrdersTableSeeder::class);
    }

    /** @test */
    public function it_should_create_a_cache_entry_for_the_template_and_store_the_right_order_values()
    {
        $order = Order::orderByDesc('id')->first();
        $company = factory(Company::class)->create();
        $template = factory(DictionaryItem::class)->create(['t_company_id' => $company->id]);
        $tmsProvider = factory(TMSProvider::class)->create();
        DictionaryCacheDefinition::create([
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'use_variant_name' => true,
            'use_bill_to_address_raw_text' => true,
            'use_event1_address_raw_text' => true,
            'use_event2_address_raw_text' => false,
        ]);
        $orderAddressEvent = factory(OrderAddressEvent::class)->create(['t_order_id' => $order->id]);
        $order->update([
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
            'tms_template_dictid_verified' => false,
        ]);

        $this->putJson(route('orders.update', $order->id), [
                'tms_template_dictid_verified' => true,
                'tms_template_dictid' => $template->id,
            ])
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseCount('t_dictionary_cache_entries', 1);
        $this->assertDatabaseHas('t_dictionary_cache_entries', [
            't_dictionary_item_id' => $template->id,
            't_company_id' => $company->id,
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'verified_count' => 1,
            'cached_variant_name' => $order->variant_name,
            'cached_bill_to_address_raw_text' => $order->bill_to_address_raw_text,
            'cached_event1_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'cached_event2_address_raw_text' => null,
        ]);
    }

    /** @test */
    public function it_should_update_the_verify_count_if_the_tms_template_was_already_verified()
    {
        $order = Order::orderByDesc('id')->first();
        $company = factory(Company::class)->create();
        $template = factory(DictionaryItem::class)->create(['t_company_id' => $company->id]);
        $tmsProvider = factory(TMSProvider::class)->create();
        DictionaryCacheDefinition::create([
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'use_variant_name' => true,
            'use_bill_to_address_raw_text' => true,
            'use_event1_address_raw_text' => true,
            'use_event2_address_raw_text' => false,
        ]);
        $orderAddressEvent = factory(OrderAddressEvent::class)->create(['t_order_id' => $order->id]);
        $order->update([
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
            'tms_template_dictid_verified' => false,
        ]);
        $this->withoutExceptionHandling();
        DictionaryCacheEntry::create([
            't_dictionary_item_id' => $template->id,
            't_company_id' => $company->id,
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'verified_count' => 1,
            'cached_variant_name' => $order->variant_name,
            'cached_bill_to_address_raw_text' => $order->bill_to_address_raw_text,
            'cached_event1_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'cached_event2_address_raw_text' => null,
        ]);

        $this->putJson(route('orders.update', $order->id), [
            'tms_template_dictid_verified' => true,
            'tms_template_dictid' => $template->id,
        ])
        ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseCount('t_dictionary_cache_entries', 1);
        $this->assertDatabaseHas('t_dictionary_cache_entries', [
            't_dictionary_item_id' => $template->id,
            't_company_id' => $company->id,
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'verified_count' => 2,
            'cached_variant_name' => $order->variant_name,
            'cached_bill_to_address_raw_text' => $order->bill_to_address_raw_text,
            'cached_event1_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'cached_event2_address_raw_text' => null,
        ]);
    }
}
