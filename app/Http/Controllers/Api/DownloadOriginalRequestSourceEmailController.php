<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;
use App\Actions\PresignImageUrl;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
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

        return (new PresignImageUrl())(
            $bucketName,
            $fileName,
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
