<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Order;
use Illuminate\Http\Response;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuditLogsControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_returns_the_audit_logs_for_the_order()
    {
        Config::set('audit.console', true);
        $this->loginAdmin();

        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();

        $order = Order::orderByDesc('id')->first();
        $order->update([
            'vessel' => 'newvessel',
            'booking_number' => 'newbooking',
            'pickup_number' => 'newpickup',
        ]);

        $this->get(route('audit-logs.index', [
            'model_type' => 'order', 'model_id' => $order->id
        ]))->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data' => [
                'order' => [
                    'vessel' => [
                        '*' => ['new', 'old', 'user', 'attribute', 'updated_at'],
                    ],
                    'booking_number' => [
                        '*' => ['new', 'old', 'user', 'attribute', 'updated_at'],
                    ],
                ]
            ]
        ]);
    }
}
