<?php

namespace App\Actions;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use App\Models\OCRRequestStatus;

class PublishSnsMessageToSendToClient
{
    public function __invoke(array $data)
    {
        try {
            $response = $this->getSnsClient()
                ->publish([
                    'Message' => json_encode([
                        'request_id' => $data['request_id'],
                        'datetime_utciso' => now()->toISOString(),
                        'status' => OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_COMPLETE,
                        'status_metadata' => $data['status_metadata']
                    ]),
                    'MessageAttributes' => [
                        'status' => [
                            'DataType' => 'String',
                            'StringValue' => OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_COMPLETE,
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
