<?php

namespace Tests\Feature\Actions;

use Mockery;
use Aws\Result;
use Tests\TestCase;
use Aws\MockHandler;
use Illuminate\Support\Str;
use App\Actions\PublishSnsMessageToUpdateStatus;

class PublishSnsMessageToUpdateStatusTest extends TestCase
{
    /** @test */
    public function it_should_send_a_sns_notification_to_the_sns_update_status_queue()
    {
        $messageId = Str::random(5);

        $mockHandler = tap(new MockHandler())
            ->append(new Result(['MessageId' => $messageId]));
        $snsClient = $this->app['aws']->createClient('sns');
        $snsClient->getHandlerList()->setHandler($mockHandler);

        $data = [
            'request_id' => 'someid',
            'order_id' => 'anotherid',
            'company_id' => 'somecompany',
            'status' => 'somestatus',
            'status_metadata' => [],
        ];
        $mockAction = Mockery::mock(PublishSnsMessageToUpdateStatus::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $mockAction->shouldReceive('getSnsClient')->andReturn($snsClient)->once();

        $response = $mockAction($data);

        $this->assertEquals($messageId, $response['message']);
    }
}
