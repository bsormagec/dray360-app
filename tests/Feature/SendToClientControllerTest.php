<?php

namespace Tests\Feature;

use Mockery;
use Aws\Result;
use Tests\TestCase;
use Aws\MockHandler;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Tests\Seeds\CompaniesSeeder;
use Tests\Seeds\OrdersTableSeeder;
use App\Actions\PublishSnsMessageToSendToClient;
use App\Actions\PublishSnsMessageToFinishRequestReview;
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

        $mockHandler = tap(new MockHandler())
            ->append(new Result(['MessageId' => $messageId]));
        $mockHandler->append(new Result(['MessageId' => $messageId]));
        $snsClient = $this->app['aws']->createClient('sns');
        $snsClient->getHandlerList()->setHandler($mockHandler);

        $mockAction = Mockery::mock(PublishSnsMessageToSendToClient::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $mockAction->shouldReceive('getSnsClient')->andReturn($snsClient)->once();
        $this->app->instance(PublishSnsMessageToSendToClient::class, $mockAction);

        $mockAction2 = Mockery::mock(PublishSnsMessageToFinishRequestReview::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $mockAction2->shouldReceive('getSnsClient')->andReturn($snsClient)->once();
        $this->app->instance(PublishSnsMessageToFinishRequestReview::class, $mockAction2);

        $this->postJson(route('orders.send-to-client', $order->id))
            ->assertJsonFragment(['data' => $messageId])
            ->assertStatus(Response::HTTP_OK);
    }
}
