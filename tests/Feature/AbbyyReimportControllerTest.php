<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\OCRRequest;
use Illuminate\Http\Response;
use Tests\Seeds\OcrRequestSeeder;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\AbbyyReimportController;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AbbyyReimportControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_call_the_api_to_run_the_re_import_from_abby()
    {
        $this->loginAdmin();
        (new OcrRequestSeeder())->seedOcrJob_ocrPostProcessingComplete();
        $ocrRequest = OCRRequest::orderByDesc('id')->first();
        config()->set('services.dray360-api.api_key', 'testApiKey');

        Http::fake();

        $this->postJson(route('ocr.requests.reimport-abbyy', $ocrRequest->request_id))
            ->assertStatus(Response::HTTP_OK);

        Http::assertSent(function (Request $request) use ($ocrRequest) {
            return $request->hasHeader('X-API-KEY', 'testApiKey') &&
                $request['function'] == AbbyyReimportController::REIMPORT_ABBYY_FUNCTION &&
                $request['request_id'] == $ocrRequest->request_id;
        });
    }
}
