<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderStatusHistoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_the_status_history_for_the_display_status_of_an_order()
    {
        $this->markTestSkipped('skipping this test until Santiago is back');
        $this->loginAdmin();
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();
        $order = Order::latest()->first();

        $this->getJson(route('orders.status-history', $order->id))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'status',
                        'start_date',
                        'end_date',
                        'diff_for_humans',
                    ],
                ],
            ]);
    }
}
