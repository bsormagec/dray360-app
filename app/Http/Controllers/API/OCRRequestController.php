<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class OCRRequestController extends Controller
{
    // only these image file extension types are accepted
    const VALID_IMAGE_TYPES = ['jpeg', 'png', 'jpg', 'tif', 'tiff', 'bmp', 'pdf'];

    // use this suffix on all uploaded files (to trigger S3 lambda process)
    const MANUAL_UPLOAD_PREFIX = 'intakeupload/';
    const MANUAL_UPLOAD_SUFFIX = '.apiupload';

    // expire upload URI after this many minutes
    const MINUTES_URI_REMAINS_VALID = 15;

    /**
     * Create URI for uploading a document to create an OCRRequest
     *
     * @param  [Request] $request
     * @param  [string] $request->filename
     *
     * @return [json] Request Id (UUID)
     */
    public function createOCRRequestUploadURI(Request $request)
    {
        try {

            // validate that filename parameter was provided
            $request->validate([
                'filename' => 'required|string'
            ]);
            $origFilename = $request->filename;

            // check file extension, return error if not of a valid type
            $extensionLC = strtolower((new \SplFileInfo($origFilename))->getExtension());
            $validExtension = in_array($extensionLC, self::VALID_IMAGE_TYPES);
            if (!$validExtension) {
                $extensionList = implode(',', self::VALID_IMAGE_TYPES);
                return response()->json([
                    'error' => 'Invalid image file type',
                    'filename' => $origFilename,
                    'message' => "Invalid file extension '{$extensionLC}'. Must be one of: '{$extensionList}'"
                ], 400);
            }

            // create a new UUID for this file upload, to be the permanent request_id
            $request_id = Str::uuid()->toString();

            // compute the new filename for upload
            $fixedFilename = preg_replace("/[^.\-\w]+/", "", $origFilename); // only dot, dash, a-Z, 0-9
            $prefix = self::MANUAL_UPLOAD_PREFIX;
            $suffix = self::MANUAL_UPLOAD_SUFFIX;
            $uploadingFilename = "{$prefix}{$request_id}.{$fixedFilename}{$suffix}";

            // Create an S3 client and get a presigned URL for uploading the image
            $s3Client = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION', 'us-east-2')
            ]);
            $s3Command = $s3Client->getCommand('PutObject', [
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $uploadingFilename
            ]);
            $urlExpiryTime = Carbon::now()->addMinutes(self::MINUTES_URI_REMAINS_VALID);
            $presignedRequest = $s3Client->createPresignedRequest($s3Command, $urlExpiryTime);
            $uploadUri = (string) $presignedRequest->getUri()->__toString();

            // response data, persisted and returned
            $responseData = [
                'request_id' => $request_id,
                'original_filename' => $origFilename,
                'uploading_filename' => $uploadingFilename,
                'url_expiry_time' => $urlExpiryTime,
                'upload_uri' => $uploadUri
            ];

            // save a new row to the t_job_state_changes table
            $this->persistOCRRequestStatus($responseData);

            // all done
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

    /**
     * Save a row to the t_job_state_changes table, which
     * is the source for the v_status_summary view, which is
     * the source for the OCRRequestStatus objects.
     *
     * @param  [Request] $request_id
     */
    public function persistOCRRequestStatus($responseData)
    {
        $data = [
            'request_id' => $responseData['request_id'],
            'status_date' => Carbon::now(),
            'status' => 'upload-requested',
            'status_metadata' => json_encode($responseData)
        ];

        DB::table('t_job_state_changes')->insert($data);
    }
}
