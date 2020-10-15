<?php

namespace App\Actions;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use App\Models\OCRRequestStatus;

class PublishSnsMessageToReprocessRequest
{
    public function __invoke(OCRRequestStatus $status)
    {
        try {
            $response = $this->getSnsClient()
                ->publish([
                    'Message' => json_encode([
                        'request_id' => $status->request_id,
                        'datetime_utciso' => now()->toISOString(),
                        'status' => OCRRequestStatus::OCR_COMPLETED,
                        'status_metadata' => $status->status_metadata,
                    ]),
                    'MessageAttributes' => [
                        'status' => [
                            'DataType' => 'String',
                            'StringValue' => OCRRequestStatus::OCR_COMPLETED,
                        ],
                        'company_id' => [
                            'DataType' => 'String',
                            'StringValue' => $status->company_id,
                        ],
                        'order_id' => [
                            'DataType' => 'String',
                            'StringValue' => ' ',
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
