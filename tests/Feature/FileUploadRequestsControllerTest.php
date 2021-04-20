<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\Order;
use App\Models\Company;
use App\Models\OCRRequest;
use Illuminate\Http\Response;
use App\Models\DictionaryItem;
use Tests\Seeds\OcrRequestSeeder;
use Tests\Seeds\OrdersTableSeeder;
use App\Actions\GenerateUploadPresignedUrl;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileUploadRequestsControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $filename = 'testfile.jpg';

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();

        $mockAction = Mockery::mock(GenerateUploadPresignedUrl::class)->makePartial();
        $mockAction->shouldReceive('__invoke')
            ->andReturn([
                'status' => 'ok',
                'data' => [
                    'upload_uri' => 'https://theurlfortheupload.com',
                    'url_expiry_time' => now(),
                    'uploading_filename' => 'thechangedname.jpg',
                ],
            ])
            ->once();
        $this->app->instance(GenerateUploadPresignedUrl::class, $mockAction);
    }

    /** @test */
    public function it_should_return_a_presigned_url_for_a_pt_imagetype()
    {
        $company = factory(Company::class)->create();
        $filename = 'testfile.jpg';

        $this->postJson(route('file-upload-requests.store'), [
                'filename' => $filename,
                'type' => DictionaryItem::PT_IMAGETYPE_TYPE,
                'company_id' => $company->id,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'request_id',
                    'original_filename',
                    'uploading_filename',
                    'url_expiry_time',
                    'upload_uri',
                    'datetime_utciso',
                ]
            ]);
    }

    /** @test */
    public function it_should_use_the_provided_request_id()
    {
        (new OcrRequestSeeder())->seedOcrJob_ocrPostProcessingComplete();
        $ocrRequest = OCRRequest::first();
        $company = factory(Company::class)->create();
        $filename = 'testfile.jpg';


        $this->postJson(route('file-upload-requests.store'), [
                'filename' => $filename,
                'type' => DictionaryItem::PT_IMAGETYPE_TYPE,
                'company_id' => $company->id,
                'request_id' => $ocrRequest->request_id,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'request_id' => $ocrRequest->request_id,
            ]);
    }

    /** @test */
    public function it_should_use_the_provided_request_id_throught_an_order_id()
    {
        (new OrdersTableSeeder())->seedOrderWithPostProcessingComplete();
        $order = Order::first();
        $company = factory(Company::class)->create();
        $filename = 'testfile.jpg';


        $this->postJson(route('file-upload-requests.store'), [
                'filename' => $filename,
                'type' => DictionaryItem::PT_IMAGETYPE_TYPE,
                'company_id' => $company->id,
                'order_id' => $order->id,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'request_id' => $order->request_id,
            ]);
    }
}
