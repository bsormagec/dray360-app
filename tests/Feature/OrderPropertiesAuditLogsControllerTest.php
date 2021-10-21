<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Company;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderPropertiesAuditLogsControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_the_list_of_changes_made_to_the_order()
    {
        $this->loginAdmin();
        Config::set('audit.console', true);

        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();

        $order = Order::orderByDesc('id')->first();
        $order->update([
            't_company_id' => factory(Company::class)->create()->id,
            'tms_shipment_id' => 123,
        ]);

        $order->update(['vessel' => 'newvessel']);
        $order->update(['vessel' => 'newvessel2']);
        $order->update(['vessel' => 'newvessel3']);

        $this->getJson(route('audit-logs-order-properties.index', [
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
                'property' => 'vessel',
            ]))
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'property_name',
                        'old_value',
                        'new_value',
                        'company_name',
                        'request_id',
                        'order_id',
                        'order_date',
                        'verifier',
                        'variant_id',
                        'variant_name',
                        'audit_id',
                        'edit_date',
                        'user',
                    ],
                ],
            ]);
    }
}
