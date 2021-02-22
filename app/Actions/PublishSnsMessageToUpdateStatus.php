<?php

namespace App\Actions;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

class PublishSnsMessageToUpdateStatus
{
    public function __invoke(array $data)
    {
        $data['order_id'] = $data['order_id'] ?? ' '; // If not present, MUST send a whitespace string for microservice to store order_id as NULL.
        $data['order_id'] = $data['order_id'] == '' ? ' ' : $data['order_id'];

        try {
            $response = $this->getSnsClient()
                ->publish([
                    'Message' => json_encode([
                        'request_id' => $data['request_id'],
                        'datetime_utciso' => now()->toISOString(),
                        'status' => $data['status'],
                        'status_metadata' => $data['status_metadata']
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
                    'MessageGroupId' => $data['request_id'],
                ]);
            return ['status' => 'ok', 'message' => $response['MessageId']];
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
