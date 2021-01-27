<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\Order;
use Illuminate\Support\Str;
use Tests\Seeds\OrdersTableSeeder;
use App\Actions\PublishSnsMessageToUpdateStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReplicateOrdersControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
    }

    /** @test */
    public function it_should_replicate_an_order_with_its_related_data_except_some_fields_and_notify_sns()
    {
        (new OrdersTableSeeder())->seedOrderWithValidatedAddresses();
        $order = Order::latest()->first(['id', 'request_id']);
        $order->update([
            'tms_shipment_id' => 'test',
            'unit_number' => 'unit',
            'tms_submission_datetime' => now(),
            'tms_cancelled_datetime' => now(),
            'seal_number' => 'somenumber',
        ]);
        $messageId = Str::random(5);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'ok', 'message' => $messageId])->twice();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->postJson(route('orders.replicate', $order->id))
            ->assertStatus(200)
            ->assertJsonFragment(['data' => $messageId]);

        $this->assertDatabaseCount('t_orders', 2);
        $this->assertDatabaseHas('t_orders', [
            'id' => $order->id + 1,
            'tms_shipment_id' => null,
            'unit_number' => null,
            'tms_submission_datetime' => null,
            'tms_cancelled_datetime' => null,
            'seal_number' => null,
            'request_id' => $order->request_id,
        ]);
        $newOrder = Order::orderByDesc('id')->first(['id', 'request_id']);
        $this->assertEquals($order->orderAddressEvents()->count(), $newOrder->orderAddressEvents()->count());
        $this->assertEquals($order->orderLineItems()->count(), $newOrder->orderLineItems()->count());
    }
}
