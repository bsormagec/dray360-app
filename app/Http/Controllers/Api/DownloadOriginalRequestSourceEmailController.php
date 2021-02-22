<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;

class DownloadOriginalRequestSourceEmailController extends Controller
{
    const MINUTES_URI_REMAINS_VALID = 15;

    public function __invoke($requestId)
    {
        $status = OCRRequestStatus::query()
            ->where('request_id', $requestId)
            ->whereIn('status', [
                OCRRequestStatus::INTAKE_STARTED,
                OCRRequestStatus::INTAKE_REJECTED
            ])
            ->first();

        if (! $status) {
            abort(404, 'No email was found for the given order.');
        }

        $this->authorize('downloadSourceFile', $status);

        return response()->json([
            'data' => $this->getTemporaryDownloadUrl($status)
        ]);
    }

    protected function getTemporaryDownloadUrl(OCRRequestStatus $status): string
    {
        $bucketName = $status->status_metadata['bucket_name'] ?? Arr::get($status->status_metadata, 'event_info.bucket_name');
        $fileName = $status->status_metadata['key_name'] ?? Arr::get($status->status_metadata, 'event_info.object_key');

        if (! $bucketName || ! $fileName) {
            abort(404, 'No email was found for the given order.');
        }

        $s3Config = config('filesystems.disks.s3-base') + ['bucket' => $bucketName];
        $storage = Storage::createS3Driver($s3Config);

        return  $storage->temporaryUrl(
            $fileName,
            now()->addMinutes(self::MINUTES_URI_REMAINS_VALID),
            [
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => HeaderUtils::makeDisposition(
                    'attachment',
                    $status->request_id . '.eml'
                ),
            ]
        );
    }
}
