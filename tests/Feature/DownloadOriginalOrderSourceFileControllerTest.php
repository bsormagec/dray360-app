<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Company;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DownloadOriginalOrderSourceFileControllerTest extends TestCase
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

        $this->get(route('orders.download-source-file', $order->id))
            ->assertJsonFragment([
                'data' => $signedUrl
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_should_fail_if_the_order_doesnt_belongs_to_the_user_company()
    {
        $this->seedTestUsers();
        $company1 = factory(Company::class)->create();
        $company2 = factory(Company::class)->create();
        $user = factory(User::class)->create(['t_company_id' => $company1->id]);
        $user->attachRole('customer-admin');
        $order = factory(Order::class)->create(['t_company_id' => $company2->id]);

        Sanctum::actingAs($user);
        Storage::fake('s3');

        $this->get(route('orders.download-source-file', $order->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
