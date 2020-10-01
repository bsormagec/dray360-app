<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;

class DownloadOriginalOrderPdfController extends Controller
{
    const MINUTES_URI_REMAINS_VALID = 15;

    public function __invoke(Order $order)
    {
        $this->authorize('downloadPdf', $order);
        $status = OCRRequestStatus::where([
            'request_id' => $order->request_id,
            'status' => OCRRequestStatus::INTAKE_ACCEPTED,
        ])->first();

        if (! $status || ! isset($status->status_metadata['document_archive_location'])) {
            abort(404, 'No file was found for the given order.');
        }

        return response()->json([
            'data' => $this->getTemporaryDownloadUrl($status)
        ]);
    }

    protected function getTemporaryDownloadUrl(OCRRequestStatus $status): string
    {
        $s3Config = config('filesystems.disks.s3-base') + [
            'bucket' => s3_bucket_from_url($status->status_metadata['document_archive_location']),
        ];
        $storage = Storage::createS3Driver($s3Config);

        return  $storage->temporaryUrl(
            s3_file_name_from_url($status->status_metadata['document_archive_location']),
            now()->addMinutes(self::MINUTES_URI_REMAINS_VALID),
            [
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => HeaderUtils::makeDisposition(
                    'attachment',
                    $status->status_metadata['original_filename'] ?? 'original.pdf'
                ),
            ]
        );
    }
}
