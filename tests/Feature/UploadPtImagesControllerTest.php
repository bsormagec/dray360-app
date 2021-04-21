<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Models\DictionaryItem;
use Tests\Seeds\CompaniesSeeder;
use Tests\Seeds\OcrRequestSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use App\Actions\PublishSnsMessageToUpdateStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UploadPtImagesControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(CompaniesSeeder::class);
        Event::fake();
    }

    /** @test */
    public function it_publishes_a_message_to_sns_update_gateway_without_an_order()
    {
        $messageId = Str::random(5);
        $requestId = Str::uuid()->toString();
        $company = Company::first();
        $dictionaryItem = factory(DictionaryItem::class)->create(['t_company_id' => $company->id]);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'ok', 'message' => $messageId])->once();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->withoutExceptionHandling();

        $this->postJson(route('upload-pt-images.store'), [
                'request_id' => $requestId,
                'original_filename' => 'filename.jpg',
                'uploading_filename' => 'someprefix/filename.jpg.somesuffix',
                'url_expiry_time' => now()->addMinutes(10)->toISOString(),
                'upload_uri' => 'https://theurltoupload.com',
                'datetime_utciso' => now()->toISOString(),
                'company_id' => $company->id,
                'tms_shipment_id' => Str::random(),
                'pt_image_type' => $dictionaryItem->id,
                'order_id' => null,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['data' => $messageId]);
    }

    /** @test */
    public function it_manages_exceptions_thrown_by_the_sns_client()
    {
        $company = Company::first();
        $dictionaryItem = factory(DictionaryItem::class)->create(['t_company_id' => $company->id]);
        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'error', 'message' => 'exception'])->once();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->postJson(route('upload-pt-images.store'), [
                'request_id' => Str::uuid()->toString(),
                'original_filename' => 'filename.jpg',
                'uploading_filename' => 'someprefix/filename.jpg.somesuffix',
                'url_expiry_time' => now()->addMinutes(10)->toISOString(),
                'upload_uri' => 'https://theurltoupload.com',
                'datetime_utciso' => now()->toISOString(),
                'company_id' => $company->id,
                'tms_shipment_id' => Str::random(),
                'pt_image_type' => $dictionaryItem->id,
            ])
            ->assertJsonFragment(['data' => 'exception'])
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_send_to_tms()
    {
        $company = Company::first();
        $dictionaryItem = factory(DictionaryItem::class)->create(['t_company_id' => $company->id]);
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldNotReceive('__invoke');
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->postJson(route('upload-pt-images.store'), [
                'request_id' => Str::uuid()->toString(),
                'original_filename' => 'filename.jpg',
                'uploading_filename' => 'someprefix/filename.jpg.somesuffix',
                'url_expiry_time' => now()->addMinutes(10)->toISOString(),
                'upload_uri' => 'https://theurltoupload.com',
                'datetime_utciso' => now()->toISOString(),
                'company_id' => $company->id,
                'tms_shipment_id' => Str::random(),
                'pt_image_type' => $dictionaryItem->id,
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_fail_if_order_is_from_other_company()
    {
        $company1 = factory(Company::class)->create();
        $company2 = factory(Company::class)->create();
        $user = factory(User::class)->create(['t_company_id' => $company1->id]);
        $user->attachRole('customer-admin');
        $dictionaryItem = factory(DictionaryItem::class)->create(['t_company_id' => $company2->id]);

        Sanctum::actingAs($user);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldNotReceive('__invoke');
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->postJson(route('upload-pt-images.store'), [
                'request_id' => Str::uuid()->toString(),
                'original_filename' => 'filename.jpg',
                'uploading_filename' => 'someprefix/filename.jpg.somesuffix',
                'url_expiry_time' => now()->addMinutes(10)->toISOString(),
                'upload_uri' => 'https://theurltoupload.com',
                'datetime_utciso' => now()->toISOString(),
                'company_id' => $company2->id,
                'tms_shipment_id' => Str::random(),
                'pt_image_type' => $dictionaryItem->id,
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_returns_the_presigned_image_of_a_pt_image_upload_request()
    {
        $requestId = (new OcrRequestSeeder())->seedPtImageUploadSucceeded();

        Storage::shouldReceive('createS3Driver')->andReturn(Storage::getFacadeRoot());
        Storage::shouldReceive('temporaryUrl')->andReturn('http://thesignedurl.com');

        $this->getJson(route('upload-pt-images.show', $requestId))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['presigned_image_url' => 'http://thesignedurl.com'])
            ->assertJsonStructure([
                'data' => [
                    'presigned_image_url',
                    'user' => ['name'],
                    'status',
                    'status_metadata',
                ],
            ]);
    }
}
