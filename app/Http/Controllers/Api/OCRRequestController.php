<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class OCRRequestController extends Controller
{
    // only these image file extension types are accepted
    const VALID_IMAGE_TYPES = ['jpeg', 'png', 'jpg', 'tif', 'tiff', 'bmp', 'pdf'];

    // use this suffix on all uploaded files (to trigger S3 lambda process)
    const MANUAL_UPLOAD_PREFIX = 'intakeupload/';
    const MANUAL_UPLOAD_SUFFIX = '.apiupload';

    // expire upload URI after this many minutes
    const MINUTES_URI_REMAINS_VALID = 15;

    public function createOCRRequestUploadURI(Request $request)
    {
        // validate that filename parameter was provided
        $request->validate(['filename' => 'required|string']);
        $originalFilename = $request->filename;
        $extension = strtolower((new \SplFileInfo($originalFilename))->getExtension());

        if (! in_array($extension, self::VALID_IMAGE_TYPES)) {
            $extensionList = implode(',', self::VALID_IMAGE_TYPES);
            throw ValidationException::withMessages([
                'filename' => "Invalid file extension '{$extension}'. Must be one of: '{$extensionList}'",
            ]);
        }

        try {
            $requestId = Str::uuid()->toString();
            $expiryTime = Carbon::now()->addMinutes(self::MINUTES_URI_REMAINS_VALID);
            $uploadingFilename = $this->getUploadingFilename($requestId, $originalFilename);
            $uploadRequestUri = $this->getUploadRequestUri($uploadingFilename, $expiryTime);
            $responseData = [
                'request_id' => $requestId,
                'original_filename' => $originalFilename,
                'uploading_filename' => $uploadingFilename,
                'url_expiry_time' => $expiryTime,
                'upload_uri' => $uploadRequestUri,
                'company_id' => currentCompany()->id ?? null,
                'user_id' => auth()->user()->id,
            ];

            OCRRequestStatus::createUploadRequest($responseData);

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'exception' => 'error processing request',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    protected function getUploadingFilename(string $requestId, string $originalFilename): string
    {
        $fixedFilename = preg_replace("/[^.\-\w]+/", "", $originalFilename); // only dot, dash, a-Z, 0-9

        return self::MANUAL_UPLOAD_PREFIX
            . "{$requestId}.{$fixedFilename}"
            . self::MANUAL_UPLOAD_SUFFIX;
    }

    protected function getUploadRequestUri($filename, $expiryTime): string
    {
        // Create an S3 client and get a presigned URL for uploading the image
        $s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => config('aws.region', 'us-east-2'),
        ]);
        $s3Command = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $filename
        ]);

        return (string) $s3Client->createPresignedRequest($s3Command, $expiryTime)
            ->getUri()
            ->__toString();
    }
}
