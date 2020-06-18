<?php

namespace Tests\Feature;

use Mockery;
use Aws\Result;
use Tests\TestCase;
use Aws\MockHandler;
use App\Models\Order;
use OrdersTableSeeder;
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use ProfitToolsCushingSeeder;
use Aws\Exception\AwsException;
use App\Actions\PublishSnsMessageToSendToTms;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SendToTmsControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected Order $order;
    protected Company $company;
    protected TMSProvider $tmsProvider;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(OrdersTableSeeder::class);
        $this->seed(ProfitToolsCushingSeeder::class);
        $this->order = Order::first();
        $this->company = Company::getCushing();
        $this->tmsProvider = TMSProvider::getProfitTools();
    }

    /** @test */
    public function it_publishs_a_message_to_sns_update_gateway()
    {
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

        $this->postJson(route('send-to-tms.store'), [
                'status' => 'sending-to-wint',
                'order_id' => $this->order->id,
                'company_id' => $this->company->id,
                'tms_provider_id' => $this->tmsProvider->id,
            ])
            ->assertJsonFragment(['data' => $messageId])
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_manages_exceptions_thrown_by_the_sns_client()
    {
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

        $this->postJson(route('send-to-tms.store'), [
                'status' => 'sending-to-wint',
                'order_id' => $this->order->id,
                'company_id' => $this->company->id,
                'tms_provider_id' => $this->tmsProvider->id,
            ])
            ->assertJsonFragment(['data' => 'failed-some aws exception'])
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
