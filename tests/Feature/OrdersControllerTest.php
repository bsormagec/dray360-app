<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use OrdersTableSeeder;
use App\Models\OCRRequest;
use Laravel\Sanctum\Sanctum;
use App\Models\OrderLineItem;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Models\OrderAddressEvent;
use Illuminate\Support\Facades\DB;
use Bezhanov\Faker\Provider\Commerce;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrdersControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(OrdersTableSeeder::class);
        $this->faker->addProvider(new Commerce($this->faker));
    }

    /** @test */
    public function it_should_list_all_the_ocr_request_with_the_orders_and_return_ocr_requests_one_per_order()
    {
        $this->withoutExceptionHandling();
        $this->getJson(route('orders.index'))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'display_id',
                        'request_id',
                        't_job_state_changes_id',
                        't_order_id',
                        'created_at',
                        'updated_at',
                        'order' => [],
                        'latest_ocr_request_status' => [
                            'display_status',
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
        factory(OCRRequestStatus::class, 2)->create(['status' => OCRRequestStatus::OCR_WAITING]);
        $order = Order::latest()->first();
        $ocrRequest = OCRRequest::latest()->first();
        $ocrRequest->created_at = now()->subDays(5);
        $ocrRequest->save();

        $this->getJson(route('orders.index', ['filter[status]' => OCRRequestStatus::OCR_WAITING]))
            ->assertJsonCount(2, 'data');
        $this->getJson(route('orders.index', [
                'filter[display_status]' => OCRRequestStatus::STATUS_MAP[OCRRequestStatus::OCR_WAITING]
            ]))
            ->assertJsonCount(2, 'data');
        $this->getJson(route('orders.index', ['filter[request_id]' => $order->request_id]))
            ->assertJsonCount(1, 'data');
        $this->getJson(route('orders.index', [
                'filter[order.bill_to_address_raw_text]' => substr($order->bill_to_address_raw_text, 0, 15)
            ]))
            ->assertJsonCount(1, 'data');
        $this->getJson(route('orders.index', [
                'filter[order.equipment_type]' => substr($order->equipment_type, 0, 5)
            ]))
            ->assertJsonCount(Order::where('equipment_type', $order->equipment_type)->count(), 'data');
        $this->getJson(route('orders.index', [
                'filter[order.shipment_designation]' => substr($order->shipment_designation, 0, 5)
            ]))
            ->assertJsonCount(Order::where('shipment_designation', $order->shipment_designation)->count(), 'data');
        $this->getJson(route('orders.index', [
                'filter[order.shipment_direction]' => substr($order->shipment_direction, 0, 5)
            ]))
            ->assertJsonCount(Order::where('shipment_direction', $order->shipment_direction)->count(), 'data');
        $dateRange = now()->subDays(6)->toDateString() . ','. now()->subDays(3)->toDateString();
        $this->getJson(route('orders.index', ['filter[created_between]' => $dateRange]))
            ->assertJsonCount(1, 'data');
        $this->getJson(route('orders.index', ['sort' => '-status']))
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function it_filters_by_the_raw_addresses_from_a_single_query_filter()
    {
        $this->withoutExceptionHandling();
        $this->seed(OrdersTableSeeder::class);
        $this->seed(OrdersTableSeeder::class);
        factory(OCRRequestStatus::class, 2)->create(['status' => OCRRequestStatus::OCR_WAITING]);
        $order = Order::latest()->first();

        $this->getJson(route('orders.index', ['filter[query]' => $order->request_id]))
            ->assertJsonCount(1, 'data');
        $this->getJson(route('orders.index', [
                'filter[query]' => substr($order->bill_to_address_raw_text, 0, 15)
            ]))
            ->assertJsonCount(1, 'data');
        $this->getJson(route('orders.index', [
                'filter[query]' => substr($order->equipment_type, 0, 5)
            ]))
            ->assertJsonCount(Order::where('equipment_type', $order->equipment_type)->count(), 'data');
    }

    /** @test */
    public function it_fails_if_not_autorized_to_filter_by_status()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $this->seed(OrdersTableSeeder::class);
        $this->seed(OrdersTableSeeder::class);
        factory(OCRRequestStatus::class, 2)->create(['status' => OCRRequestStatus::OCR_WAITING]);

        $this->getJson(route('orders.index', ['filter[status]' => OCRRequestStatus::OCR_WAITING]))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_only_update_the_allowed_order_fields()
    {
        $originalOrder = Order::orderByDesc('id')->first();
        $data = $this->fillOrderUpdate($originalOrder);

        $this->putJson(route('orders.update', $originalOrder->id), $data)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas(
            't_orders',
            [
                'request_id' => $originalOrder->request_id,
                'bill_to_address_raw_text' => $originalOrder->bill_to_address_raw_text,
                'port_ramp_of_origin_address_raw_text' => $originalOrder->port_ramp_of_origin_address_raw_text,
                'port_ramp_of_destination_address_raw_text' => $originalOrder->port_ramp_of_destination_address_raw_text,
            ]
            + collect($data)
                ->reject(function ($item, $key) {
                    return in_array($key, [
                        'created_at',
                        'updated_at',
                        'deleted_at',
                        'ocr_data',
                    ]);
                })
                ->toArray()
        );
        $originalOrder->refresh();
        $this->assertJsonStringNotEqualsJsonString(
            json_encode($originalOrder->ocr_data),
            json_encode($data['ocr_data'])
        );
    }

    /** @test */
    public function it_fails_if_not_autorized_to_update_order()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $originalOrder = Order::orderByDesc('id')->first();
        $data = $this->fillOrderUpdate($originalOrder);

        $this->putJson(route('orders.update', $originalOrder->id), $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_create_a_new_related_editable_relationships_or_update_them_if_they_already_exists()
    {
        $order = Order::orderByDesc('id')->first();
        $orderLineItems = $order->orderLineItems->toArray();
        $orderLineItems[] = $this->makeFakeDataFor(OrderLineItem::class, $order);
        $orderLineItems[0]['contents'] = $this->faker->productName;

        $orderAddressEvents = [factory(OrderAddressEvent::class)->create(['t_order_id' => $order->id])->toArray()];
        $orderAddressEvents[] = $this->makeFakeDataFor(OrderAddressEvent::class, $order);
        $orderAddressEvents[0]['address_schedule_description'] = $this->faker->address;

        $this->putJson(route('orders.update', $order->id), [
                'order_line_items' => $orderLineItems,
                'order_address_events' => $orderAddressEvents,
            ])
            ->assertStatus(Response::HTTP_OK);

        collect([
            't_order_line_items' => $orderLineItems,
            't_order_address_events' => $orderAddressEvents,
        ])->flatMap(function ($items, $table) {
            return collect($items)
                ->map(fn ($item) => ['table' => $table, 'data' => $item])
                ->toArray();
        })->each(function ($data) {
            $this->assertDatabaseHas(
                $data['table'],
                collect($data['data'])
                    ->reject(function ($item, $key) {
                        return in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at', 'event_number']);
                    })
                    ->map(fn ($item) => is_bool($item) ? intval($item) : $item)
                    ->toArray()
            );
        });
    }

    /** @test */
    public function it_should_soft_delete_the_order_line_items_if_the_deleted_at_is_set()
    {
        $order = Order::orderByDesc('id')->first();
        $orderLineItems = $order->orderLineItems->toArray();
        $orderLineItems[0]['deleted_at'] = true;

        $orderAddressEvents = [factory(OrderAddressEvent::class)->create(['t_order_id' => $order->id])->toArray()];
        $orderAddressEvents[0]['deleted_at'] = true;

        $this->putJson(route('orders.update', $order->id), [
                'order_line_items' => $orderLineItems,
                'order_address_events' => $orderAddressEvents,
            ])
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals(0, $order->orderLineItems()->count());
        $this->assertEquals(1, $order->orderLineItems()->onlyTrashed()->count());
        $this->assertEquals(0, $order->orderAddressEvents()->count());
        $this->assertEquals(1, $order->orderAddressEvents()->onlyTrashed()->count());
    }

    /** @test */
    public function it_should_return_the_order_with_presigned_url()
    {
        Storage::fake();
        $order = Order::orderByDesc('id')->first();
        $order->ocr_data = [
            "page_index_filenames" => [
                "name" => "page_index_filenames",
                "value" => [
                    "1" => [
                        "name" => "page_index_1",
                        "value" => "s3://dmedocproc-ocrprocessedjobs-dev/e061c2fb-7ec3-596b-acdc-95b461f27e2b_daedc679e9e9b441d98c0634e83ae24bab8722a385058db6f9e08f545e770f1d_ShipmentCartageAdviceWithReceipt-SSI100072169.00000001.jpg"
                    ]
                ]
            ]
        ];
        $order->save();

        Storage::shouldReceive('createS3Driver')->andReturn(Storage::getFacadeRoot());
        Storage::shouldReceive('temporaryUrl')->andReturn('http://thesignedurl.com');

        $this->getJson(route('orders.show', $order->id))
            ->assertJsonStructure([
                'id',
                'request_id',
                'ocr_data',
                'ocr_request',
                'order_line_items',
                'bill_to_address',
                'port_ramp_of_destination_address',
                'port_ramp_of_origin_address',
                'order_address_events',
            ])
            ->assertJsonFragment(['presigned_download_uri' => 'http://thesignedurl.com']);
    }

    protected function fillOrderUpdate($original)
    {
        $ocrRequestId = $this->faker->uuid;

        DB::table('t_job_state_changes')->insert([
            'request_id' => $ocrRequestId,
            'status_date' => now()->subMinutes(5)->toDateTimeString(),
            'status' => 'intake-started',
            'status_metadata' => '{"event_info": {"event_time": "2019-12-06T20:28:59.595Z", "object_key": "intakeemail/4tckssjbuh0c2dt8rlund3efvcd4g6pmjeagee81", "bucket_name": "dmedocproc-emailintake-dev", "aws_request_id": "'.$ocrRequestId.'", "log_group_name": "/aws/lambda/intake-filter-dev", "log_stream_name": "2019/12/06/[$LATEST]55e4fa95494f4364a68a85e537e8e3fa", "event_time_epoch_ms": 1575664139000}, "request_id": "'.$ocrRequestId.'", "source_summary": {"source_type": "email", "source_email_subject": "Fwd: test 202", "source_email_to_address": "dev@docprocessing.draymaster.com", "source_email_from_address": "Peter Nelson <peter@peternelson.com>", "source_email_body_prefixes": ["b\'---------- Forwarded message ---------\\r\\nFrom: Peter Nelson <peter@peternelson.com>\\r\\nDate: Fri, Dec 6, 2019 at 1:43 PM\\r\\nSubject: test 202\\r\\nTo: Peter B. Nelson <peter@peternelson.com>\\r\\n\'", "b\'<div dir=\"ltr\"><div class=\"gmail_default\" style=\"font-size:small\"><br></div><br><div class=\"gmail_quote\"><div dir=\"ltr\" class=\"gmail_attr\">---------- Forwarded message ---------<br>From: <b class=\"gmail_sendername\" dir=\"auto\">Peter Nelson</b> <span dir=\"auto\">&lt;<a href=\"mailto:peter@peternelson.com\">peter@peternelson.com</a>&gt;</span><br>Date: Fri, Dec 6, 2019 at 1:43 PM<br>Subject: test 202<br>To: Peter B. Nelson &lt;<a href=\"mailto:peter@peternelson.com\">peter@peternelson.com</a>&gt;<br><"], "source_email_string_length": 164489, "source_email_attachment_filenames": ["MATSON-examplar.pdf"]}, "read_log_commandline": "aws --profile=draymaster logs get-log-events --log-group-name=\'/aws/lambda/intake-filter-dev\' --log-stream-name=\'2019/12/06/[$LATEST]55e4fa95494f4364a68a85e537e8e3fa\' --start-time=\'1575664139000\'"}',
        ]);

        return collect($original->toArray())
            ->merge(
                factory(Order::class)->make(['request_id' => $ocrRequestId])->toArray()
            )
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
