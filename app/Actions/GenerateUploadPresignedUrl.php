<?php

namespace App\Actions;

use Exception;
use Aws\S3\S3Client;
use App\Models\ObjectLock;
use App\Models\DictionaryItem;
use Illuminate\Support\Carbon;

class GenerateUploadPresignedUrl
{
    const MINUTES_URI_REMAINS_VALID = 15;

    public function __invoke(array $data): array
    {
        try {
            $expiryTime = Carbon::now()->addMinutes(self::MINUTES_URI_REMAINS_VALID);
            $uploadingFilename = $this->getUploadingFilename($data['request_id'], $data['filename'], $data['type']);
            $url = $this->getUploadRequestUri($uploadingFilename, $expiryTime);

            return [
                'status' => 'ok',
                'data' => [
                    'upload_uri' => $url,
                    'url_expiry_time' => $expiryTime,
                    'uploading_filename' => $uploadingFilename,
                ],
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'data' => [
                    'message' => $e->getMessage(),
                    'exception' => 'error processing request',
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ],
            ];
        }
    }

    protected function getUploadingFilename(string $requestId, string $originalFilename, string $type): string
    {
        $fixedFilename = preg_replace("/[^.\-\w]+/", "", $originalFilename); // only dot, dash, a-Z, 0-9
        $uploadPrefixes = [
            DictionaryItem::PT_IMAGETYPE_TYPE => 'imageupload/',
            ObjectLock::REQUEST_OBJECT => 'intakeupload/',
        ];
        $uploadSuffixes = [
            DictionaryItem::PT_IMAGETYPE_TYPE => '.apiupload',
            ObjectLock::REQUEST_OBJECT => '.apiupload',
        ];

        return $uploadPrefixes[$type] . "{$requestId}.{$fixedFilename}" . $uploadSuffixes[$type];
    }

    protected function getUploadRequestUri(string $filename, Carbon $expiryTime): string
    {
        // Create an S3 client and get a presigned URL for uploading the image
        $s3Client = $this->getS3Client([
            'version' => 'latest',
            'region' => config('aws.region', 'us-east-2'),
        ]);
        $s3Command = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $filename
        ]);

        return (string) $s3Client->createPresignedRequest($s3Command, $expiryTime)->getUri();
    }

    protected function getS3Client(array $args = []): S3Client
    {
        return app('aws')->createClient('s3', $args);
    }
}
