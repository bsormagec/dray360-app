<?php

namespace Tests\Feature\Listeners;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Company;
use App\Models\TMSProvider;
use App\Models\DictionaryItem;
use App\Models\OrderAddressEvent;
use Tests\Seeds\OrdersTableSeeder;
use App\Models\DictionaryCacheEntry;
use App\Models\DictionaryCacheDefinition;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateOrUpdateCacheEntryTest extends TestCase
{
    use DatabaseTransactions;

    protected Order $order;
    protected Company $company;
    protected DictionaryItem $template;
    protected TMSProvider $tmsProvider;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(OrdersTableSeeder::class);

        DictionaryCacheDefinition::updateOrCreate(
            ['cache_type' => DictionaryItem::TEMPLATE_TYPE, ],
            [
                    'use_variant_name' => true,
                    'use_bill_to_address_raw_text' => true,
                    'use_event1_address_raw_text' => true,
                    'use_event2_address_raw_text' => false,
                    'use_event3_address_raw_text' => false,
                    'use_hazardous' => true,
                    'use_equipment_size' => true,
                    'use_vessel' => false,
                    'use_carrier' => true,
                    'use_shipment_direction' => true,
                    'use_template_key' => true,
            ]
        );
        $this->company = factory(Company::class)->create();
        $this->order = Order::orderByDesc('id')->first();
        $this->template = factory(DictionaryItem::class)->create(['t_company_id' => $this->company->id]);
        $this->tmsProvider = factory(TMSProvider::class)->create();

        $this->order->update([
            't_company_id' => $this->company->id,
            't_tms_provider_id' => $this->tmsProvider->id,
            'tms_template_dictid' => $this->template->id,
            'tms_template_dictid_verified' => false,
            'hazardous' => 1,
            'equipment_size' => '123',
            'vessel' => 'somevessel',
            'carrier' => '345',
            'shipment_direction' => 'top',
        ]);
    }

    /** @test */
    public function it_should_create_a_cache_entry_for_the_template_and_store_the_right_order_values()
    {
        $orderAddressEvent = factory(OrderAddressEvent::class)->create(['t_order_id' => $this->order->id]);

        $this->order->update([
            'tms_template_dictid_verified' => true,
        ]);

        $this->assertDatabaseCount('t_dictionary_cache_entries', 1);
        $this->assertDatabaseHas('t_dictionary_cache_entries', [
            't_dictionary_item_id' => $this->template->id,
            't_company_id' => $this->company->id,
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'verified_count' => 1,
            'cached_variant_name' => $this->order->variant_name,
            'cached_bill_to_address_raw_text' => $this->order->bill_to_address_raw_text,
            'cached_event1_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'cached_event2_address_raw_text' => null,
            'cached_event3_address_raw_text' => null,
            'cached_hazardous' => $this->order->hazardous,
            'cached_equipment_size' => $this->order->equipment_size,
            'cached_vessel' => null,
            'cached_carrier' => $this->order->carrier,
            'cached_shipment_direction' => $this->order->shipment_direction,
            'cached_template_key' => $this->template->item_key,
        ]);
    }

    /** @test */
    public function it_should_update_the_verify_count_if_the_tms_template_was_already_verified()
    {
        $orderAddressEvent = factory(OrderAddressEvent::class)->create(['t_order_id' => $this->order->id]);
        DictionaryCacheEntry::create([
            't_dictionary_item_id' => $this->template->id,
            't_company_id' => $this->company->id,
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'verified_count' => 1,
            'cached_variant_name' => $this->order->variant_name,
            'cached_bill_to_address_raw_text' => $this->order->bill_to_address_raw_text,
            'cached_event1_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'cached_event2_address_raw_text' => null,
            'cached_event3_address_raw_text' => null,
            'cached_hazardous' => $this->order->hazardous,
            'cached_equipment_size' => $this->order->equipment_size,
            'cached_vessel' => null,
            'cached_carrier' => $this->order->carrier,
            'cached_shipment_direction' => $this->order->shipment_direction,
            'cached_template_key' => $this->template->item_key,
        ]);

        $this->order->update([
            'tms_template_dictid_verified' => true,
        ]);

        $this->assertDatabaseCount('t_dictionary_cache_entries', 1);
        $this->assertDatabaseHas('t_dictionary_cache_entries', [
            't_dictionary_item_id' => $this->template->id,
            't_company_id' => $this->company->id,
            'cache_type' => DictionaryItem::TEMPLATE_TYPE,
            'verified_count' => 2,
            'cached_variant_name' => $this->order->variant_name,
            'cached_bill_to_address_raw_text' => $this->order->bill_to_address_raw_text,
            'cached_event1_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'cached_event2_address_raw_text' => null,
            'cached_event3_address_raw_text' => null,
            'cached_hazardous' => $this->order->hazardous,
            'cached_equipment_size' => $this->order->equipment_size,
            'cached_vessel' => null,
            'cached_carrier' => $this->order->carrier,
            'cached_shipment_direction' => $this->order->shipment_direction,
            'cached_template_key' => $this->template->item_key,
        ]);
    }
}
