<?php

namespace Tests\Feature;

use Mockery;
use Aws\Result;
use Tests\TestCase;
use App\Models\User;
use Aws\MockHandler;
use App\Models\Order;
use OrdersTableSeeder;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use ProfitToolsCushingSeeder;
use Aws\Exception\AwsException;
use App\Actions\PublishSnsMessageToSendToTms;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SendToTmsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(ProfitToolsCushingSeeder::class);
    }

    /** @test */
    public function it_publishs_a_message_to_sns_update_gateway()
    {
        (new OrdersTableSeeder())->seedOrderWithValidatedAddresses();
        $order = Order::first();
        $messageId = Str::random(5);

        $mockHandler = tap(new MockHandler())
            ->append(new Result(['MessageId' => $messageId]));
        $snsClient = $this->app['aws']->createClient('sns');
        $snsClient->getHandlerList()->setHandler($mockHandler);

        $mockAction = Mockery::mock(PublishSnsMessageToSendToTms::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $mockAction->shouldReceive('getSnsClient')->andReturn($snsClient);
        $this->app->instance(PublishSnsMessageToSendToTms::class, $mockAction);

        $this->postJson(route('send-to-tms'), [
                'status' => 'sending-to-wint',
                'order_id' => $order->id,
            ])
            ->assertJsonFragment(['data' => $messageId])
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_manages_exceptions_thrown_by_the_sns_client()
    {
        (new OrdersTableSeeder())->seedOrderWithValidatedAddresses();
        $order = Order::first();
        $this->withoutExceptionHandling();
        $mockHandler = tap(new MockHandler())
            ->append(function ($cmd) {
                return new AwsException('', $cmd, [
                    'code' => 'failed',
                    'message' => 'some aws exception',
                ]);
            });
        $snsClient = $this->app['aws']->createClient('sns');
        $snsClient->getHandlerList()->setHandler($mockHandler);

        $mockAction = Mockery::mock(PublishSnsMessageToSendToTms::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $mockAction->shouldReceive('getSnsClient')->andReturn($snsClient);
        $this->app->instance(PublishSnsMessageToSendToTms::class, $mockAction);

        $this->postJson(route('send-to-tms'), [
                'status' => 'sending-to-wint',
                'order_id' => $order->id,
            ])
            ->assertJsonFragment(['data' => 'failed-some aws exception'])
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_send_to_tms()
    {
        $this->seed(OrdersTableSeeder::class);
        $order = Order::first();
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $mockHandler = tap(new MockHandler())
            ->append(function ($cmd) {
                return new AwsException('', $cmd, [
                    'code' => 'failed',
                    'message' => 'some aws exception',
                ]);
            });
        $snsClient = $this->app['aws']->createClient('sns');
        $snsClient->getHandlerList()->setHandler($mockHandler);

        $mockAction = Mockery::mock(PublishSnsMessageToSendToTms::class)
            ->shouldAllowMockingProtectedMethods();
        $mockAction->shouldNotReceive('getSnsClient');
        $this->app->instance(PublishSnsMessageToSendToTms::class, $mockAction);

        $this->postJson(route('send-to-tms'), [
                'status' => 'sending-to-wint',
                'order_id' => $order->id,
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_fail_if_the_order_doesnt_have_all_the_addresses_validated()
    {
        $this->markTestSkipped('validation is disabled for now');
        (new OrdersTableSeeder())->seedOrderWithoutValidatedAddresses();
        $order = Order::first();

        $mockHandler = tap(new MockHandler())
            ->append(function ($cmd) {
                return new AwsException('', $cmd, [
                    'code' => 'failed',
                    'message' => 'some aws exception',
                ]);
            });
        $snsClient = $this->app['aws']->createClient('sns');
        $snsClient->getHandlerList()->setHandler($mockHandler);

        $mockAction = Mockery::mock(PublishSnsMessageToSendToTms::class)
            ->shouldAllowMockingProtectedMethods();
        $mockAction->shouldNotReceive('getSnsClient');
        $this->app->instance(PublishSnsMessageToSendToTms::class, $mockAction);

        $this->postJson(route('send-to-tms'), [
                'status' => 'sending-to-wint',
                'order_id' => $order->id,
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'port_ramp_of_destination_address_verified',
                'port_ramp_of_origin_address_verified',
                'bill_to_address_verified',
                'order_address_events.*.t_address_verified',
            ]);
    }
}
