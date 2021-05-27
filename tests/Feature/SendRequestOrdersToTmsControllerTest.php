<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\TMSProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Actions\SendOrderToTms;
use Tests\Seeds\CompaniesSeeder;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SendRequestOrdersToTmsControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $requestId;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(CompaniesSeeder::class);
        Event::fake();

        $this->requestId = Str::uuid()->toString();
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete($this->requestId);
        (new OrdersTableSeeder())->seedOrderWithSuccessSendingToWint($this->requestId);
        Order::get()->each(function ($order) {
            $order->update([
                't_tms_provider_id' => TMSProvider::getProfitTools()->id,
                'tms_template_dictid_verified' => false,
                'carrier_dictid_verified' => false,
                'vessel_dictid_verified' => false,
            ]);
        });
    }

    /** @test */
    public function it_sends_to_tms_only_the_orders_that_can_be_send_to_the_tms()
    {
        $messageId = Str::random(5);

        $mockAction = Mockery::mock(SendOrderToTms::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'ok', 'message' => $messageId])->once();
        $this->app->instance(SendOrderToTms::class, $mockAction);

        $this->postJson(route('ocr.requests.send-to-tms', $this->requestId))
            ->assertJsonFragment([
                'sent' => 1,
                'not_sent' => 1,
                'failed' => 0,
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_manages_exceptions_thrown_by_the_sns_client()
    {
        $mockAction = Mockery::mock(SendOrderToTms::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'error', 'message' => 'exception'])->once();
        $this->app->instance(SendOrderToTms::class, $mockAction);

        $this->postJson(route('ocr.requests.send-to-tms', $this->requestId))
            ->assertJsonFragment([
                'sent' => 0,
                'not_sent' => 1,
                'failed' => 1,
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_send_to_tms()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $mockAction = Mockery::mock(SendOrderToTms::class)->makePartial();
        $mockAction->shouldNotReceive('__invoke');
        $this->app->instance(SendOrderToTms::class, $mockAction);

        $this->postJson(route('ocr.requests.send-to-tms', $this->requestId))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
