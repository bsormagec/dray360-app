<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use Tests\Seeds\OcrRequestSeeder;
use App\Actions\PublishSnsMessageToUpdateStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OcrRequestReprocessControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        (new OcrRequestSeeder())->seedOcrJob_ocrPostProcessingComplete();
    }

    /** @test */
    public function it_should_fire_a_sns_message_to_replicate_the_request_ocr_completed_status()
    {
        $status = OCRRequestStatus::where('status', OCRRequestStatus::OCR_COMPLETED)->first();
        $messageId = Str::random(5);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'ok', 'message' => $messageId])->once();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->postJson(route('ocr.requests.reprocess', $status->request_id))
            ->assertJsonFragment(['data' => $messageId])
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_should_fail_if_request_doesnt_exist()
    {
        $this->postJson(route('ocr.requests.reprocess', Str::uuid()))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_should_fail_if_the_user_is_not_authorized()
    {
        $this->loginCustomerAdmin();
        $status = OCRRequestStatus::where('status', OCRRequestStatus::OCR_COMPLETED)->first();

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldNotReceive('__invoke');
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->postJson(route('ocr.requests.reprocess', $status->request_id))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_fail_if_the_service_fails()
    {
        $status = OCRRequestStatus::where('status', OCRRequestStatus::OCR_COMPLETED)->first();

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction->shouldReceive('__invoke')->andReturn(['status' => 'error', 'message' => '-'])->once();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->postJson(route('ocr.requests.reprocess', $status->request_id))
            ->assertJsonFragment(['data' => '-'])
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
