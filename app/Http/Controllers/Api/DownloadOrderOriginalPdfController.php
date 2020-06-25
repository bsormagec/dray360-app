<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Support\Str;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;

class DownloadOrderOriginalPdfController extends Controller
{
    public function __invoke(Order $order)
    {
        $status = OCRRequestStatus::where([
            'request_id' => $order->request_id,
            'status' => OCRRequestStatus::INTAKE_ACCEPTED,
        ])->first();

        if (! $status || ! isset($status->status_metadata['document_archive_location'])) {
            abort(404, 'No file was found for the given order.');
        }

        return redirect($this->getTemporaryDownloadUrl($status));
    }

    protected function getTemporaryDownloadUrl(OCRRequestStatus $status): string
    {
        $fileName = Str::after(
            $status->status_metadata['document_archive_location'],
            config('filesystems.disks.s3.bucket') .'/'
        );

        return  Storage::temporaryUrl(
            $fileName,
            now()->addMinutes(1),
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
