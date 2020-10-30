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
                        'company_id' => $data['company_id'],
                        'tms_provider_id' => $data['tms_provider_id'],
                        'datetime_utciso' => now()->toISOString(),
                        'status' => $data['status'],
                        'status_metadata' => ['order_id' => $data['order_id']]
                    ]),
                    'MessageAttributes' => [
                        'status' => [
                            'DataType' => 'String',
                            'StringValue' => $data['status'],
                        ],
                        'company_id' => [
                            'DataType' => 'String',
                            'StringValue' => $data['company_id'],
                        ],
                        'order_id' => [
                            'DataType' => 'String',
                            'StringValue' => $data['order_id'],
                        ],
                    ],
                    'TopicArn' => config('services.sns-topics.status'),
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
