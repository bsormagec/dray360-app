<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\OCRRequest;
use App\Models\OCRRequestStatus;
use Tests\Seeds\OcrRequestSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OcrRequestControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
    }

    /** @test */
    public function it_should_return_the_right_information_about_the_request()
    {
        (new OcrRequestSeeder())->seedOcrJob_ocrPostProcessingComplete();

        $this->getJson(route('ocr.requests.index'))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'request_id',
                        'created_at',
                        'updated_at',
                        'email_from_address',
                        'upload_user_name',
                        'latest_ocr_request_status',
                        'first_order_bill_to_address_id',
                        'first_order_bill_to_address',
                        'first_order_id',
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_should_allow_filtering_the_requests()
    {
        (new OcrRequestSeeder())->seedOcrJob_intakeRejected();
        (new OcrRequestSeeder())->seedOcrJob_ocrPostProcessingComplete();
        (new OcrRequestSeeder())->seedOcrJob_ocrWaiting();
        (new OcrRequestSeeder())->seedOcrJob_ocrWaiting();
        $ocrRequest = OCRRequest::latest()->first();
        $ocrRequest->created_at = now()->subDays(5);
        $ocrRequest->save();

        $this->getJson(route('ocr.requests.index', ['filter[status]' => OCRRequestStatus::OCR_WAITING]))
            ->assertJsonCount(2, 'data');
        $this->getJson(route('ocr.requests.index', [
                'filter[display_status]' => OCRRequestStatus::STATUS_MAP[OCRRequestStatus::OCR_WAITING]
            ]))
            ->assertJsonCount(2, 'data');
        $this->getJson(route('ocr.requests.index', ['filter[request_id]' => $ocrRequest->request_id]))
            ->assertJsonCount(1, 'data');
        $dateRange = now()->subDays(6)->toDateString() . ','. now()->subDays(3)->toDateString();
        $this->getJson(route('ocr.requests.index', ['filter[created_between]' => $dateRange]))
            ->assertJsonCount(1, 'data');
        $this->getJson(route('ocr.requests.index', ['sort' => '-status']))
            ->assertJsonCount(4, 'data');
    }

    /** @test */
    public function it_should_list_only_current_company_requests_if_not_superadmin()
    {
        $this->loginCustomerAdmin();
        $user = auth()->user();
        $this->seed(OcrRequestSeeder::class);
        $ocrRequest = OCRRequest::first();
        $ocrRequest->latestOcrRequestStatus->update(['company_id' => $user->getCompanyId()]);

        $this->getJson(route('ocr.requests.index'))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_should_list_all_the_request_if_not_superadmin()
    {
        $this->loginCustomerAdmin();
        $user = auth()->user();
        $this->seed(OcrRequestSeeder::class);
        $ocrRequest = OCRRequest::first();
        $ocrRequest->latestOcrRequestStatus->update(['company_id' => $user->getCompanyId()]);
        $this->loginAdmin();

        $this->getJson(route('ocr.requests.index'))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
    }
}
