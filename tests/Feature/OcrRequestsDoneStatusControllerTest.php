<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\OCRRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use Tests\Seeds\OcrRequestSeeder;
use App\Actions\PublishSnsMessageToUpdateStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OcrRequestsDoneStatusControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_change_the_done_at_column_and_puiblish_a_message_to_sns()
    {
        $this->loginAdmin();
        (new OcrRequestSeeder())->seedOcrJob_ocrPostProcessingComplete();
        $ocrRequest = OCRRequest::orderByDesc('id')->first();
        $messageId = Str::random(5);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction
            ->shouldReceive('__invoke')
            ->withArgs(function ($args) {
                return $args['status'] == OCRRequestStatus::REQUEST_MARKED_DONE;
            })
            ->andReturn(['status' => 'ok', 'message' => $messageId])
            ->once();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->put(route('ocr.requests.done-status', $ocrRequest->request_id), ['done' => true])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data']);

        $this->assertDatabaseMissing('t_job_latest_state', [
            'request_id' => $ocrRequest->request_id,
            'order_id' => null,
            'done_at' => null,
        ]);
        $this->assertNotNull($ocrRequest->refresh()->done_at);
    }

    /** @test */
    public function it_should_mark_as_undone_the_done_at_and_send_sns_notification()
    {
        $this->loginAdmin();
        (new OcrRequestSeeder())->seedOcrJob_ocrPostProcessingComplete();
        $ocrRequest = OCRRequest::orderByDesc('id')->first();
        $ocrRequest->update(['done_at' => now()]);
        $messageId = Str::random(5);

        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)->makePartial();
        $mockAction
            ->shouldReceive('__invoke')
            ->withArgs(function ($args) {
                return $args['status'] == OCRRequestStatus::REQUEST_MARKED_UNDONE;
            })
            ->andReturn(['status' => 'ok', 'message' => $messageId])
            ->once();
        $this->app->instance(PublishSnsMessageToUpdateStatus::class, $mockAction);

        $this->put(route('ocr.requests.done-status', $ocrRequest->request_id), ['done' => false])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data']);

        $this->assertDatabaseHas('t_job_latest_state', [
            'request_id' => $ocrRequest->request_id,
            'order_id' => null,
            'done_at' => null,
        ]);
        $this->assertNull($ocrRequest->refresh()->done_at);
    }
}
