<?php

namespace App\Actions;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

class PublishSnsMessageToSendToTms
{
    public function __invoke(array $data)
    {
        try {
            $response = $this->getSnsClient()
                ->publish([
                    'Message' => json_encode([
                        'request_id' => $data['request_id'],
                        'datetime_utciso' => now()->toISOString(),
                        'status' => $data['status'],
                        'status_metadata' => ['order_id' => $data['order_id']]
                    ]),
                    'MessageAttributes' => [
                        'status' => [
                            'DataType' => 'String',
                            'StringValue' => $data['status'],
                        ],
                    ],
                    'TopicArn' => config('services.sns-topics.send-to-tms'),
                ]);
            return ['status' => 'ok', 'message' => $response['MessageId'], ];
        } catch (AwsException $e) {
            return [
                'status' => 'error',
                'message' => $e->getAwsErrorCode()."-".$e->getAwsErrorMessage(),
            ];
        }
    }

    protected function getSnsClient(): SnsClient
    {
        return app('aws')->createClient('sns');
    }
}
