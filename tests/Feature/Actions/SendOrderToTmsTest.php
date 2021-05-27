<?php

namespace Tests\Feature\Actions;

use Mockery;
use Tests\TestCase;
use App\Models\Order;
use App\Models\TMSProvider;
use Illuminate\Support\Str;
use App\Actions\SendOrderToTms;
use Tests\Seeds\CompaniesSeeder;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Support\Facades\Event;
use App\Actions\PublishSnsMessageToUpdateStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SendOrderToTmsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(CompaniesSeeder::class);
        Event::fake();
    }

    /** @test */
    public function it_sends_the_given_order_to_the_tms()
    {
        (new OrdersTableSeeder())->seedOrderWithValidatedAddresses();
        $order = Order::first();
        $order->update([
            't_tms_provider_id' => TMSProvider::getProfitTools()->id,
            'tms_template_dictid_verified' => false,
            'carrier_dictid_verified' => false,
            'vessel_dictid_verified' => false,
        ]);
        $messageId = Str::random(5);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'ok', 'message' => $messageId])->once();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $response = (new SendOrderToTms())($order);

        $this->assertEquals($messageId, $response['message']);
        $this->assertDatabaseHas('t_orders', [
            'id' => $order->id,
            'tms_template_dictid_verified' => true,
            'carrier_dictid_verified' => true,
            'vessel_dictid_verified' => true,
        ]);
    }

    /** @test */
    public function it_manages_exceptions_thrown_by_the_sns_client()
    {
        (new OrdersTableSeeder())->seedOrderWithValidatedAddresses();
        $order = Order::first();
        $order->update([
            't_tms_provider_id' => TMSProvider::getProfitTools()->id,
            'bill_to_address_verified' => false,
            'equipment_type_verified' => false,
            'tms_template_dictid_verified' => false,
            'carrier_dictid_verified' => false,
            'vessel_dictid_verified' => false,
        ]);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'error', 'message' => 'exception'])->once();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $response = (new SendOrderToTms())($order);

        $this->assertEquals('exception', $response['message']);

        $this->assertDatabaseHas('t_orders', [
            'id' => $order->id,
            'bill_to_address_verified' => false,
            'equipment_type_verified' => false,
            'tms_template_dictid_verified' => false,
            'carrier_dictid_verified' => false,
            'vessel_dictid_verified' => false,
        ]);
    }

    /** @test */
    public function it_should_fail_if_the_order_doesnt_have_all_the_addresses_validated()
    {
        $this->markTestSkipped('validation is disabled for now');
        (new OrdersTableSeeder())->seedOrderWithoutValidatedAddresses();
        $order = Order::first();

        $mockAction = Mockery::mock(SendOrderToTms::class)->makePartial();
        $mockAction->shouldNotReceive('__invoke');
        $this->app->instance(SendOrderToTms::class, $mockAction);

        $this->postJson(route('orders.send-to-tms', $order->id))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'port_ramp_of_destination_address_verified',
                'port_ramp_of_origin_address_verified',
                'bill_to_address_verified',
                'order_address_events.*.t_address_verified',
            ]);
    }
}
