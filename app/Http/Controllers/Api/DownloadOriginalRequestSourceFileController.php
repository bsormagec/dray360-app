<?php

namespace App\Http\Controllers\Api;

use App\Actions\PresignImageUrl;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\HeaderUtils;

class DownloadOriginalRequestSourceFileController extends Controller
{
    const MINUTES_URI_REMAINS_VALID = 15;

    public function __invoke($requestId)
    {
        $status = OCRRequestStatus::query()
            ->where('request_id', $requestId)
            ->whereIn('status', [
                OCRRequestStatus::INTAKE_ACCEPTED,
                OCRRequestStatus::INTAKE_ACCEPTED_DATAFILE
            ])
            ->first();

        if (! $status || ! isset($status->status_metadata['document_archive_location'])) {
            abort(404, 'No file was found for the given order.');
        }

        $this->authorize('downloadSourceFile', $status);


        return response()->json([
            'data' => $this->getTemporaryDownloadUrl($status)
        ]);
    }

    protected function getTemporaryDownloadUrl(OCRRequestStatus $status): string
    {
        return (new PresignImageUrl())(
            s3_bucket_from_url($status->status_metadata['document_archive_location']),
            s3_file_name_from_url($status->status_metadata['document_archive_location']),
            [
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => HeaderUtils::makeDisposition(
                    'attachment',
                    preg_replace(
                        '/[[:cntrl:]]/',
                        '',
                        $status->status_metadata['original_filename'] ?? 'original.pdf'
                    )
                ),
            ]
        );
    }
}
