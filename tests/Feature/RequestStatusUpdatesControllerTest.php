<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\OCRRequest;
use Tests\Seeds\OcrRequestSeeder;
use App\Events\RequestStatusUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RequestStatusUpdatesControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_broadcast_a_notification_to_the_request_status_update_company_channel_and_all_companies_channel()
    {
        $requestId = (new OcrRequestSeeder())->seedOcrJob_ocrWaiting();
        $request = OCRRequest::where('request_id', $requestId)->whereNull('order_id')->first();
        Event::fake();
        Config::set('services.dray360-api.webhook_key', 'thetoken');

        $this->postJson(route('request-status-updates.store'), [
            'token' => 'thetoken',
            'request_id' => $requestId,
            'status_date' => $request->latestOcrRequestStatus->created_at,
            'status' => $request->latestOcrRequestStatus->status,
            'status_metadata' => $request->latestOcrRequestStatus->status_metadata,
            'company_id' => $request->latestOcrRequestStatus->company_id,
            'order_id' => $request->latestOcrRequestStatus->order_id,
        ]);

        Event::assertDispatched(RequestStatusUpdated::class, 1);
        Event::assertDispatched(function (RequestStatusUpdated $event) use ($request) {
            return $event->requestId == $request->request_id
                && $event->latestStatus['status'] == $request->latestOcrRequestStatus->status
                && $event->broadcastOn()[0] == 'private-request-status-updated'
                && $event->broadcastOn()[1] == ('private-request-status-updated-company'.$request->latestOcrRequestStatus->company_id);
        });
    }
}
