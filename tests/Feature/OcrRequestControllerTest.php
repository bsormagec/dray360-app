<?php

namespace Tests\Feature;

use Tests\TestCase;
use OrdersTableSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OcrRequestControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_the_right_information_about_the_request()
    {
        $this->loginAdmin();
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();
        $this->withoutExceptionHandling();

        $this->getJson(route('ocr.requests.index'))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'request_id',
                        'created_at',
                        'updated_at',
                        'email_from_address',
                        'upload_user_name',
                        'latest_ocr_request_status',
                        'first_order_bill_to_address_id',
                        'first_order_bill_to_address',
                        'first_order_id',
                    ]
                ]
            ]);
    }
}
