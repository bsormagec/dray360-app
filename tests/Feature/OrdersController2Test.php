<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\OCRRequestStatus;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrdersController2Test extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
    }

    /** @test */
    public function it_should_list_all_the_ocr_request_with_the_orders_and_return_ocr_requests_one_per_order()
    {
        (new OrdersTableSeeder())->seedOrderWithIntakeRejected();
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();

        $this->withoutExceptionHandling();
        $this->getJson(route('orders-2.index'))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'request_id',
                        'created_at',
                        'equipment_type',
                        'shipment_designation',
                        'shipment_direction',
                        'tms_shipment_id',
                        'bill_to_address_id',
                        'unit_number',
                        'reference_number',
                        'bill_to_address',
                        'has_pdf',
                        'latest_ocr_request_status' => [
                            'display_status',
                            'display_message',
                        ],
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    /** @test */
    public function it_should_allow_filtering_the_orders_and_ocr_request()
    {
        $this->withoutExceptionHandling();
        $this->seed(OrdersTableSeeder::class);
        $this->seed(OrdersTableSeeder::class);
        (new OrdersTableSeeder())->seedOrderWithOcrWaiting();
        (new OrdersTableSeeder())->seedOrderWithOcrWaiting();
        $order = Order::latest()->first();
        $order->created_at = now()->subDays(5);
        $order->save();

        $this->getJson(route('orders-2.index', [
                'filter[status]' => OCRRequestStatus::OCR_WAITING
            ]))
            ->assertJsonCount(2, 'data');
        $this->getJson(route('orders-2.index', [
                'filter[display_status]' => OCRRequestStatus::STATUS_MAP[OCRRequestStatus::OCR_WAITING]
            ]))
            ->assertJsonCount(2, 'data');
        $this->getJson(route('orders-2.index', [
                'filter[request_id]' => $order->request_id
            ]))
            ->assertJsonCount(1, 'data');
        $this->getJson(route('orders-2.index', [
                'filter[query]' => substr($order->request_id, 0, 10)
            ]))
            ->assertJsonCount(1, 'data');
        $this->getJson(route('orders-2.index', [
                'filter[query]' => $order->reference_number
            ]))
            ->assertJsonCount(1, 'data');

        $dateRange = now()->subDays(6)->toDateString() . ','. now()->subDays(3)->toDateString();
        $this->getJson(route('orders-2.index', [
                'filter[created_between]' => $dateRange
            ]))
            ->assertJsonCount(1, 'data');

        $this->getJson(route('orders-2.index', ['sort' => '-status']))
            ->assertJsonCount(4, 'data');
    }
}
