<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use Illuminate\Http\Response;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StatusHistoryControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_the_status_history_for_the_display_status_of_a_request()
    {
        $this->loginAdmin();
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();
        $order = Order::latest()->first();

        $this->getJson(route('status-history.index', ['request_id' => $order->request_id]))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'status',
                        'start_date',
                        'end_date',
                        'diff_for_humans',
                        'company_id',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_should_return_the_status_history_for_the_display_status_of_an_order()
    {
        $this->loginAdmin();
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();
        $order = Order::latest()->first();

        $this->getJson(route('status-history.index', ['order_id' => $order->id]))
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

    /** @test */
    public function it_should_return_not_found_if_order_is_deleted()
    {
        $this->loginAdmin();
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();
        $order = Order::latest()->first();
        $order->delete();

        $this->getJson(route('status-history.index', ['order_id' => $order->id]))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
