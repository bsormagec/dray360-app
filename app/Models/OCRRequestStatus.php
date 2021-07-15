<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Database\Eloquent\Collection $ocrRequests
 * @property \App\Models\OCRRequest $ocrRequest
 * @property string $request_id
 * @property string|\Carbon\Carbon $status_date
 * @property string $status
 * @property array $status_metadata
 * @property int $company_id
 * @property int $order_id
 */
class OCRRequestStatus extends Model
{
    const INTAKE_ACCEPTED = 'intake-accepted',
    INTAKE_ACCEPTED_DATAFILE = 'intake-accepted-datafile',
    INTAKE_EXCEPTION = 'intake-exception',
    INTAKE_REJECTED = 'intake-rejected',
    INTAKE_STARTED = 'intake-started',
    UPLOAD_REQUESTED = 'upload-requested',

    OCR_COMPLETED = 'ocr-completed',
    OCR_POST_PROCESSING_COMPLETE = 'ocr-post-processing-complete',
    OCR_POST_PROCESSING_ERROR = 'ocr-post-processing-error',
    OCR_POST_PROCESSING_AUTOSUBMITED = 'ocr-post-processing-autosubmited',
    OCR_TIMEDOUT = 'ocr-timedout',
    OCR_WAITING = 'ocr-waiting',
    OCR_POST_PROCESSING_REVIEW = 'ocr-post-processing-review',

    REPLICATED_FROM_EXISTING_ORDER = 'replicated-from-existing-order',

    REQUEST_MARKED_DONE = 'request-marked-done',
    REQUEST_MARKED_UNDONE = 'request-marked-undone',

    PROCESS_OCR_OUTPUT_FILE_REVIEW = 'process-ocr-output-file-review',
    PROCESS_OCR_OUTPUT_FILE_COMPLETE = 'process-ocr-output-file-complete',
    PROCESS_OCR_OUTPUT_FILE_ERROR = 'process-ocr-output-file-error',

    SENDING_TO_WINT = 'sending-to-wint',
    AUTO_SENDING_TO_WINT = 'auto-sending-to-wint',
    FAILURE_SENDING_TO_WINT = 'failure-sending-to-wint',
    SUCCESS_SENDING_TO_WINT = 'success-sending-to-wint',
    SHIPMENT_CREATED_BY_WINT = 'shipment-created-by-wint',
    SHIPMENT_NOT_CREATED_BY_WINT = 'shipment-not-created-by-wint',

    UPDATING_TO_WINT = 'updating-to-wint',
    FAILURE_UPDATING_TO_WINT = 'failure-updating-to-wint',
    SUCCESS_UPDATING_TO_WINT = 'success-updating-to-wint',
    SHIPMENT_UPDATED_BY_WINT = 'shipment-updated-by-wint',
    SHIPMENT_NOT_UPDATED_BY_WINT = 'shipment-not-updated-by-wint',

    SENDING_TO_CHAINIO = 'sending-to-chainio',
    AUTO_SENDING_TO_CHAINIO = 'auto-sending-to-chainio',
    SUCCESS_SENDING_TO_CHAINIO = 'success-sending-to-chainio',
    FAILURE_SENDING_TO_CHAINIO = 'failure-sending-to-chainio',
    SHIPMENT_CREATED_BY_CHAINIO = 'shipment-created-by-chainio',
    SHIPMENT_NOT_CREATED_BY_CHAINIO = 'shipment-not-created-by-chainio',

    UPDATES_PRIOR_ORDER = 'updates-prior-order',
    UPDATED_BY_SUBSEQUENT_ORDER = 'updated-by-subsequent-order',

    UPLOAD_IMAGE_REQUESTED = 'upload-image-requested',
    UPLOAD_IMAGE_FAILED = 'upload-image-failed',
    UPLOAD_IMAGE_SUCCEEDED = 'upload-image-succeeded',

    SUCCESS_IMAGEUPLOADING_TO_BLACKFLY = 'success-imageuploding-to-blackfl',
    FAILURE_IMAGEUPLOADING_TO_BLACKFLY = 'failure-imageuploding-to-blackfl',
    UNTRIED_IMAGEUPLOADING_TO_BLACKFLY = 'untried-imageuploding-to-blackfl',

    SENDING_TO_COMPCARE = 'sending-to-compcare',
    AUTO_SENDING_TO_COMPCARE = 'auto-sending-to-compcare',
    SUCCESS_SENDING_TO_COMPCARE = 'success-sending-to-compcare',
    FAILURE_SENDING_TO_COMPCARE = 'failure-sending-to-compcare',
    SHIPMENT_CREATED_BY_COMPCARE = 'shipment-created-by-compcare',
    SHIPMENT_NOT_CREATED_BY_COMPCARE = 'shipment-not-created-by-compcare',

    INTAKE_FILE_INGESTION = 'intake-file-ingestion'
    ;


    // These are request statuses to *not* retrieve with the OcrRequestsListQuery
    const HIDE_FROM_REQUESTS_LIST = [
        self::INTAKE_STARTED,
        self::INTAKE_FILE_INGESTION
    ];


    // This is the map of "system statuses" to "display statuses"
    const STATUS_MAP = [
        self::INTAKE_ACCEPTED => 'Processing',
        self::INTAKE_ACCEPTED_DATAFILE => 'Processing',
        self::OCR_COMPLETED => 'Processing',
        self::OCR_WAITING => 'Processing',
        self::OCR_POST_PROCESSING_REVIEW => 'Processing',

        self::UPLOAD_IMAGE_REQUESTED => 'Uploading Image',
        self::UPLOAD_IMAGE_FAILED => 'Image Upload Failed',
        self::UPLOAD_IMAGE_SUCCEEDED => 'Image Uploaded',

        self::INTAKE_EXCEPTION => 'Exception',

        self::INTAKE_STARTED => 'Intake',
        self::UPLOAD_REQUESTED => 'Intake',

        self::INTAKE_REJECTED => 'Rejected',
        self::OCR_POST_PROCESSING_ERROR => 'Rejected',
        self::OCR_TIMEDOUT => 'Rejected',
        self::PROCESS_OCR_OUTPUT_FILE_ERROR => 'Rejected',

        self::OCR_POST_PROCESSING_COMPLETE => 'Processed',
        self::PROCESS_OCR_OUTPUT_FILE_COMPLETE => 'Processed',

        self::OCR_POST_PROCESSING_AUTOSUBMITED => 'Auto Submitted',

        self::PROCESS_OCR_OUTPUT_FILE_REVIEW => 'Needs Review',

        self::REPLICATED_FROM_EXISTING_ORDER => 'Processed',

        self::UPDATES_PRIOR_ORDER => 'Update',

        self::REQUEST_MARKED_DONE => 'Marked Complete',
        self::REQUEST_MARKED_UNDONE => 'Marked not Complete',

        self::UPDATED_BY_SUBSEQUENT_ORDER => 'Replaced',

        self::SENDING_TO_WINT => 'Sending to TMS',
        self::AUTO_SENDING_TO_WINT => 'Sending to TMS',
        self::UPDATING_TO_WINT => 'Sending to TMS',
        self::SENDING_TO_CHAINIO => 'Sending to TMS',
        self::AUTO_SENDING_TO_CHAINIO => 'Sending to TMS',

        self::SUCCESS_SENDING_TO_WINT => 'Sent to TMS',
        self::SUCCESS_UPDATING_TO_WINT => 'Sent to TMS',
        self::SUCCESS_SENDING_TO_CHAINIO => 'Sent to TMS',

        self::SHIPMENT_CREATED_BY_WINT => 'Accepted by TMS',
        self::SHIPMENT_UPDATED_BY_WINT => 'Accepted by TMS',
        self::SUCCESS_IMAGEUPLOADING_TO_BLACKFLY => 'Accepted by TMS',
        self::UNTRIED_IMAGEUPLOADING_TO_BLACKFLY => 'Accepted by TMS',
        self::SHIPMENT_CREATED_BY_CHAINIO => 'Accepted by TMS',

        self::SHIPMENT_NOT_UPDATED_BY_WINT => 'TMS Warning',
        self::FAILURE_UPDATING_TO_WINT => 'TMS Warning',
        self::FAILURE_IMAGEUPLOADING_TO_BLACKFLY => 'TMS Warning',

        self::SHIPMENT_NOT_CREATED_BY_WINT => 'TMS Error',
        self::FAILURE_SENDING_TO_WINT => 'TMS Error',
        self::SHIPMENT_NOT_CREATED_BY_CHAINIO => 'TMS Error',
        self::FAILURE_SENDING_TO_CHAINIO => 'TMS Error',
    ];

    public $table = 't_job_state_changes';

    public $fillable = [
        'request_id',
        'status_date',
        'status',
        'status_metadata',
        'company_id',
        'order_id'
    ];

    protected $casts = [
        'status_metadata' => 'json',
        'status_date' => 'date',
    ];

    protected $appends = ['display_status'];

    public function ocrRequests()
    {
        return $this->belongsTo(OCRRequest::class, 'request_id', 'request_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function ocrRequest()
    {
        return $this->hasOne(OCRRequest::class, 't_job_state_changes_id');
    }

    public function getDisplayStatusAttribute()
    {
        return self::STATUS_MAP[$this->status] ?? '-';
    }

    public static function getStatusFromDisplayStatus($displayStatus): array
    {
        return collect(self::STATUS_MAP)
            ->reject(fn ($item, $key) => $item !== $displayStatus)
            ->keys()
            ->toArray();
    }

    public static function createUploadRequest(array $statusMetadata): self
    {
        $data = [
            'request_id' => $statusMetadata['request_id'],
            'status_date' => now(),
            'status' => static::UPLOAD_REQUESTED,
            'status_metadata' => $statusMetadata,
            'company_id' => $statusMetadata['company_id']
        ];

        return static::create($data);
    }

    public static function alreadyCompleted($requestId): bool
    {
        return static::query()
            ->where('request_id', $requestId)
            ->whereIn('status', [ static::OCR_POST_PROCESSING_COMPLETE, static::OCR_POST_PROCESSING_AUTOSUBMITED ])
            ->exists();
    }
}
