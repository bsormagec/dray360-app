<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use Aws\Sns\SnsClient;
use Aws\CommandInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Aws\Exception\AwsException;
use App\Models\OCRRequestStatus;
use Tests\Seeds\OcrRequestSeeder;
use App\Actions\PublishSnsMessageToReprocessRequest;
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

        $mockSnsClient = Mockery::mock(SnsClient::class);
        $mockSnsClient->shouldReceive('publish')
            ->withArgs(function ($arg) use ($status) {
                $message = json_decode($arg['Message'], true);

                return $message['request_id'] == $status->request_id
                    && $message['status'] == OCRRequestStatus::OCR_COMPLETED
                    && $message['status_metadata']['s3_bucket'] == $status->status_metadata['s3_bucket']
                    && $message['status_metadata']['s3_region'] == $status->status_metadata['s3_region']
                    && count($message['status_metadata']['file_list']) == count($status->status_metadata['file_list'])
                    && Arr::get($arg, 'MessageAttributes.status.StringValue') == OCRRequestStatus::OCR_COMPLETED
                    && Arr::get($arg, 'MessageAttributes.company_id.StringValue') == "{$status->company_id}";
            })
            ->andReturn(['MessageId' => $messageId]);

        $mockAction = Mockery::mock(PublishSnsMessageToReprocessRequest::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mockAction->shouldReceive('getSnsClient')->andReturn($mockSnsClient);
        $this->app->instance(PublishSnsMessageToReprocessRequest::class, $mockAction);

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

        $mockAction = Mockery::mock(PublishSnsMessageToReprocessRequest::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $mockAction->shouldNotReceive('getSnsClient');
        $this->app->instance(PublishSnsMessageToReprocessRequest::class, $mockAction);

        $this->postJson(route('ocr.requests.reprocess', $status->request_id))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_fail_if_the_service_fails()
    {
        $status = OCRRequestStatus::where('status', OCRRequestStatus::OCR_COMPLETED)->first();

        $mockAwsInterface = Mockery::mock(CommandInterface::class)->makePartial();
        $mockSnsClient = Mockery::mock(SnsClient::class);
        $mockSnsClient->shouldReceive('publish')
            ->andThrow(new AwsException('Error', $mockAwsInterface));

        $mockAction = Mockery::mock(PublishSnsMessageToReprocessRequest::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mockAction->shouldReceive('getSnsClient')->andReturn($mockSnsClient);
        $this->app->instance(PublishSnsMessageToReprocessRequest::class, $mockAction);

        $this->postJson(route('ocr.requests.reprocess', $status->request_id))
            ->assertJsonFragment(['data' => '-'])
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
