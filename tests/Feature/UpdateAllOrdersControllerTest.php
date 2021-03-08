<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Company;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\Seeds\OrdersTableSeeder;
use Bezhanov\Faker\Provider\Commerce;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateAllOrdersControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    protected Collection $orders;

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();
        $this->loginAdmin();
        $seeder = new OrdersTableSeeder();
        $seeder->seedOrderWithPostProcessingComplete();
        $seeder->seedOrderWithPostProcessingComplete();

        $this->orders = Order::all(['request_id', 'id']);
        $requestId = $this->orders->first()->request_id;
        $this->orders->each(fn ($order) => $order->update(['request_id' => $requestId]));

        $this->faker->addProvider(new Commerce($this->faker));
    }

    /** @test */
    public function it_should_update_all_base_orders_in_the_same_request()
    {
        $originalOrder = Order::orderByDesc('id')->first();
        $this->withoutExceptionHandling();

        $this->putJson(route('orders.update-all', $originalOrder->id), [
                'has_chassis' => 1,
                'rate_quote_number' => 123,
            ])
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals(2, Order::where([
            'has_chassis' => 1,
            'rate_quote_number' => 123,
        ])->count());
    }

    /** @test */
    public function it_should_not_update_blacklisted_parameters()
    {
        $originalOrder = Order::orderByDesc('id')->first();
        $this->withoutExceptionHandling();

        $this->putJson(route('orders.update-all', $originalOrder->id), [
                'has_chassis' => 1,
                'rate_quote_number' => 123,
                'unit_number' => 123123,
                'seal_number' => 342422,
            ])
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals(0, Order::where([
            'has_chassis' => 1,
            'rate_quote_number' => 123,
            'unit_number' => 123123,
            'seal_number' => 342422,
        ])->count());
    }

    /** @test */
    public function it_fails_if_not_autorized_to_update_order()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $originalOrder = Order::orderByDesc('id')->first();
        $data = $this->fillOrderUpdate($originalOrder);

        $this->putJson(route('orders.update-all', $originalOrder->id), $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_fail_when_trying_to_update_an_order_from_other_company()
    {
        $company1 = factory(Company::class)->create();
        $company2 = factory(Company::class)->create();
        $user = factory(User::class)->create(['t_company_id' => $company1->id]);
        $user->attachRole('customer-admin');
        $order = factory(Order::class)->create(['t_company_id' => $company2->id]);

        Sanctum::actingAs($user);

        $this->putJson(route('orders.update-all', $order->id), ['ship_comment' => 'test'])
                ->assertStatus(Response::HTTP_FORBIDDEN);

        $order->refresh();
        $this->assertNotEquals('test', $order->ship_comment);
    }

    protected function fillOrderUpdate($original)
    {
        $ocrRequestId = $this->faker->uuid;

        DB::table('t_job_state_changes')->insert([
            'request_id' => $ocrRequestId,
            'status_date' => now()->subMinutes(5)->toDateTimeString(),
            'company_id' => $original->getCompanyId() ?? factory(Company::class)->create()->id,
            'status' => 'intake-started',
            'status_metadata' => '{"event_info": {"event_time": "2019-12-06T20:28:59.595Z", "object_key": "intakeemail/4tckssjbuh0c2dt8rlund3efvcd4g6pmjeagee81", "bucket_name": "dmedocproc-emailintake-dev", "aws_request_id": "'.$ocrRequestId.'", "log_group_name": "/aws/lambda/intake-filter-dev", "log_stream_name": "2019/12/06/[$LATEST]55e4fa95494f4364a68a85e537e8e3fa", "event_time_epoch_ms": 1575664139000}, "request_id": "'.$ocrRequestId.'", "source_summary": {"source_type": "email", "source_email_subject": "Fwd: test 202", "source_email_to_address": "dev@docprocessing.draymaster.com", "source_email_from_address": "Peter Nelson <peter@peternelson.com>", "source_email_body_prefixes": ["b\'---------- Forwarded message ---------\\r\\nFrom: Peter Nelson <peter@peternelson.com>\\r\\nDate: Fri, Dec 6, 2019 at 1:43 PM\\r\\nSubject: test 202\\r\\nTo: Peter B. Nelson <peter@peternelson.com>\\r\\n\'", "b\'<div dir=\"ltr\"><div class=\"gmail_default\" style=\"font-size:small\"><br></div><br><div class=\"gmail_quote\"><div dir=\"ltr\" class=\"gmail_attr\">---------- Forwarded message ---------<br>From: <b class=\"gmail_sendername\" dir=\"auto\">Peter Nelson</b> <span dir=\"auto\">&lt;<a href=\"mailto:peter@peternelson.com\">peter@peternelson.com</a>&gt;</span><br>Date: Fri, Dec 6, 2019 at 1:43 PM<br>Subject: test 202<br>To: Peter B. Nelson &lt;<a href=\"mailto:peter@peternelson.com\">peter@peternelson.com</a>&gt;<br><"], "source_email_string_length": 164489, "source_email_attachment_filenames": ["MATSON-examplar.pdf"]}, "read_log_commandline": "aws --profile=draymaster logs get-log-events --log-group-name=\'/aws/lambda/intake-filter-dev\' --log-stream-name=\'2019/12/06/[$LATEST]55e4fa95494f4364a68a85e537e8e3fa\' --start-time=\'1575664139000\'"}',
        ]);

        return collect($original->toArray())
            ->merge(
                factory(Order::class)->make(['request_id' => $ocrRequestId])->toArray()
            )
            ->map(function ($value) {
                if (is_bool($value)) {
                    return $value === true ? 1 : 0;
                }

                return $value;
            })
            ->toArray();
    }

    protected function makeFakeDataFor(string $className, Order $order)
    {
        return factory($className)->make([
            't_order_id' => $order->id,
            'id' => null
        ])->toArray();
    }
}
