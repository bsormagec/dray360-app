<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use OrdersTableSeeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DownloadOriginalOrderPdfControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_a_redirect_to_the_amazon_signed_url_to_download_the_file()
    {
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();
        $this->loginAdmin();

        $signedUrl = 'https://s3url.com/file?signed';
        Storage::fake('s3');
        Storage::shouldReceive('createS3Driver')->andReturn(Storage::getFacadeRoot());
        Storage::shouldReceive('temporaryUrl')->andReturn($signedUrl);
        $order = Order::first(['id']);

        $this->get(route('orders.download-pdf', $order->id))
            ->assertRedirect($signedUrl);
    }
}
