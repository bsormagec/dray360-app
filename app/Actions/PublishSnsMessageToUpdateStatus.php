<?php

namespace App\Actions;

use Aws\Sns\SnsClient;
use Illuminate\Support\Str;
use Aws\Exception\AwsException;

class PublishSnsMessageToUpdateStatus
{
    public function __invoke(array $data)
    {
        $data['order_id'] = $data['order_id'] ?? ' '; // If not present, MUST send a whitespace string for microservice to store order_id as NULL.
        $data['order_id'] = $data['order_id'] == '' ? ' ' : $data['order_id'];

        try {
            $response = $this->getSnsClient()->publish($this->getMessageBody($data));

            return ['status' => 'ok', 'message' => $response['MessageId']];
        } catch (AwsException $e) {
            return [
                'status' => 'error',
                'message' => $e->getAwsErrorCode()."-".$e->getAwsErrorMessage(),
            ];
        }
    }

    protected function getMessageBody(array $data): array
    {
        $topicArn = config('services.sns-topics.status');
        $topicMessage = [
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
            'TopicArn' => $topicArn,
        ];

        if (Str::endsWith($topicArn, '.fifo')) {
            $messageGroup = $data['request_id'];

            if (trim($data['order_id']) != '') {
                $messageGroup = $messageGroup.':'.$data['order_id'];
            }

            $topicMessage['MessageGroupId'] = $messageGroup;
        }

        return $topicMessage;
    }

    protected function getSnsClient(): SnsClient
    {
        return app('aws')->createClient('sns');
    }
}
