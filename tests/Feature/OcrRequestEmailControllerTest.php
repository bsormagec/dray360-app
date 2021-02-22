<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OcrRequestEmailControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_the_email_details_of_the_given_request()
    {
        $this->loginAdmin();
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();
        $order = Order::latest()->first();

        $this->getJson(route('ocr.requests.email-details', ['request_id' => $order->request_id]))
            ->assertJsonStructure([
                'data' => [
                    'event_info',
                    'source_summary' => [
                        'source_type',
                        'source_email_subject',
                        'source_email_to_address',
                    ],
                ]
            ]);
    }
}
