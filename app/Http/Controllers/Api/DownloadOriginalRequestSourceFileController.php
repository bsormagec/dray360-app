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
            $status = OCRRequestStatus::query()
                ->where('request_id', $requestId)
                ->where('status', OCRRequestStatus::UPLOAD_REQUESTED)
                ->first();

            abort_if(! $status, 404, 'No file was found for the given order.');
        }

        $this->authorize('downloadSourceFile', $status);


        return response()->json([
            'data' => $this->getTemporaryDownloadUrl($status)
        ]);
    }

    protected function getTemporaryDownloadUrl(OCRRequestStatus $status): string
    {
        $bucketName = $status->status == OCRRequestStatus::UPLOAD_REQUESTED
            ? s3_bucket_from_url($status->status_metadata['upload_uri'])
            : s3_bucket_from_url($status->status_metadata['document_archive_location']);
        $filePath = $status->status == OCRRequestStatus::UPLOAD_REQUESTED
            ? $status->status_metadata['uploading_filename']
            : s3_file_name_from_url($status->status_metadata['document_archive_location']);

        return (new PresignImageUrl())(
            $bucketName,
            $filePath,
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
