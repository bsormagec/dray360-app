<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Tests\Seeds\CompaniesSeeder;
use Tests\Seeds\OrdersTableSeeder;
use App\Actions\PublishSnsMessageToUpdateStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SendToClientControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(CompaniesSeeder::class);
    }

    /** @test */
    public function it_publishs_a_message_to_sns_update_gateway()
    {
        $this->withoutExceptionHandling();
        (new OrdersTableSeeder())->seedOrderWithProcessOcrOutputFileReview();
        $order = Order::first();
        $messageId = Str::random(5);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'ok', 'message' => $messageId])->twice();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->postJson(route('orders.send-to-client', $order->id))
            ->assertJsonFragment(['data' => $messageId])
            ->assertStatus(Response::HTTP_OK);
    }
}
