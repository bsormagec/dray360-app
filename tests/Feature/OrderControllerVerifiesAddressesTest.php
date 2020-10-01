<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use OrdersTableSeeder;
use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Http\Response;
use App\Events\AddressVerified;
use App\Models\VerifiedAddress;
use App\Models\OrderAddressEvent;
use App\Models\CompanyAddressTMSCode;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Testing\Fakes\EventFake;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderControllerVerifiesAddressesTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(OrdersTableSeeder::class);
    }

    /** @test */
    public function it_should_dispatch_the_event_that_an_address_was_verified()
    {
        Event::swap(new EventFake(Event::getFacadeRoot()));
        $order = Order::orderByDesc('id')->first();
        $address = factory(Address::class)->create();
        $company = factory(Company::class)->create();
        $tmsProvider = factory(TMSProvider::class)->create();
        $orderAddressEvent = factory(OrderAddressEvent::class)->create([
            't_address_verified' => false,
            't_order_id' => $order->id,
        ]);
        $order->update([
            'bill_to_address_verified' => false,
            'bill_to_address_id' => $address->id,
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
        ]);

        $this->putJson(route('orders.update', $order->id), [
                'bill_to_address_verified' => true,
                'order_address_events' => [
                    $orderAddressEvent->setAttribute('t_address_verified', true)->toArray()
                ],
            ])
            ->assertStatus(Response::HTTP_OK);

        Event::assertDispatchedTimes(AddressVerified::class, 2);
    }

    /** @test */
    public function it_should_create_a_verified_address_if_it_doesnt_exists_for_billing_to_address()
    {
        $order = Order::orderByDesc('id')->first();
        $address = factory(Address::class)->create();
        $company = factory(Company::class)->create();
        $tmsProvider = factory(TMSProvider::class)->create();
        $orderAddressEvent = factory(OrderAddressEvent::class)->create([
            't_address_verified' => false,
            't_order_id' => $order->id,
        ]);
        $order->update([
            'bill_to_address_verified' => false,
            'bill_to_address_id' => $address->id,
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
            ]);
        $tmsCode = CompanyAddressTMSCode::createFrom('1', $address, $company, $tmsProvider);
        $tmsCode2 = CompanyAddressTMSCode::createFrom('1', $orderAddressEvent->t_address_id, $company, $tmsProvider);

        $this->putJson(route('orders.update', $order->id), [
                'bill_to_address_verified' => true,
                'order_address_events' => [
                    $orderAddressEvent->setAttribute('t_address_verified', true)->toArray()
                ],
            ])
            ->assertStatus(Response::HTTP_OK);


        $this->assertDatabaseCount('t_verified_addresses', 2);
        $this->assertDatabaseHas('t_verified_addresses', [
            't_company_id' => $order->t_company_id,
            't_tms_provider_id' => $order->t_tms_provider_id,
            'ocr_address_raw_text' => $order->bill_to_address_raw_text,
            'company_address_tms_code' => $tmsCode->company_address_tms_code,
            'verified_count' => 0,
            'skip_verification' => false,
        ]);
        $this->assertDatabaseHas('t_verified_addresses', [
            't_company_id' => $order->t_company_id,
            't_tms_provider_id' => $order->t_tms_provider_id,
            'ocr_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'company_address_tms_code' => $tmsCode2->company_address_tms_code,
            'verified_count' => 0,
            'skip_verification' => false,
        ]);
    }

    /** @test */
    public function it_should_update_the_verify_count_if_the_address_was_already_verified()
    {
        $order = Order::orderByDesc('id')->first();
        $address = factory(Address::class)->create();
        $company = factory(Company::class)->create(['automatic_address_verification_threshold' => 0]);
        $tmsProvider = factory(TMSProvider::class)->create();
        $orderAddressEvent = factory(OrderAddressEvent::class)->create([
            't_address_verified' => false,
            't_order_id' => $order->id,
        ]);
        $order->update([
            'bill_to_address_verified' => false,
            'bill_to_address_id' => $address->id,
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
            ]);
        $tmsCode = CompanyAddressTMSCode::createFrom('1', $address, $company, $tmsProvider);
        $tmsCode2 = CompanyAddressTMSCode::createFrom('2', $orderAddressEvent->t_address_id, $company, $tmsProvider);
        $this->withoutExceptionHandling();
        VerifiedAddress::create([
            't_company_id' => $order->t_company_id,
            't_tms_provider_id' => $order->t_tms_provider_id,
            'ocr_address_raw_text' => $order->bill_to_address_raw_text,
            'company_address_tms_code' => $tmsCode->company_address_tms_code,
            'verified_count' => 0,
            'skip_verification' => false,
        ]);
        VerifiedAddress::create([
            't_company_id' => $order->t_company_id,
            't_tms_provider_id' => $order->t_tms_provider_id,
            'ocr_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'company_address_tms_code' => $tmsCode2->company_address_tms_code,
            'verified_count' => 0,
            'skip_verification' => false,
        ]);

        $this->putJson(route('orders.update', $order->id), [
                'bill_to_address_verified' => true,
                'order_address_events' => [
                    $orderAddressEvent->setAttribute('t_address_verified', true)->toArray()
                ],
            ])
            ->assertStatus(Response::HTTP_OK);


        $this->assertDatabaseCount('t_verified_addresses', 2);
        $this->assertDatabaseHas('t_verified_addresses', [
            't_company_id' => $order->t_company_id,
            't_tms_provider_id' => $order->t_tms_provider_id,
            'ocr_address_raw_text' => $order->bill_to_address_raw_text,
            'company_address_tms_code' => $tmsCode->company_address_tms_code,
            'verified_count' => 1,
            'skip_verification' => 1,
        ]);
        $this->assertDatabaseHas('t_verified_addresses', [
            't_company_id' => $order->t_company_id,
            't_tms_provider_id' => $order->t_tms_provider_id,
            'ocr_address_raw_text' => $orderAddressEvent->t_address_raw_text,
            'company_address_tms_code' => $tmsCode2->company_address_tms_code,
            'verified_count' => 1,
            'skip_verification' => 1,
        ]);
    }
}
