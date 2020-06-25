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
 */
class OCRRequestStatus extends Model
{
    const INTAKE_ACCEPTED = 'intake-accepted',
    INTAKE_EXCEPTION = 'intake-exception',
    INTAKE_REJECTED = 'intake-rejected',
    INTAKE_STARTED = 'intake-started',
    OCR_COMPLETED = 'ocr-completed',
    OCR_POST_PROCESSING_COMPLETE = 'ocr-post-processing-complete',
    OCR_POST_PROCESSING_ERROR = 'ocr-post-processing-error',
    OCR_WAITING = 'ocr-waiting',
    PROCESS_OCR_OUTPUT_FILE_COMPLETE = 'process-ocr-output-file-complete',
    PROCESS_OCR_OUTPUT_FILE_ERROR = 'process-ocr-output-file-error',
    UPLOAD_REQUESTED = 'upload-requested';

    const STATUS_MAP = [
        self::INTAKE_ACCEPTED => 'Processing',
        self::INTAKE_EXCEPTION => 'Exception',
        self::INTAKE_REJECTED => 'Rejected',
        self::INTAKE_STARTED => 'Intake',
        self::OCR_COMPLETED => 'Processing',
        self::OCR_POST_PROCESSING_COMPLETE => 'Verified',
        self::OCR_POST_PROCESSING_ERROR => 'Rejected',
        self::OCR_WAITING => 'Processing',
        self::PROCESS_OCR_OUTPUT_FILE_COMPLETE => 'Processing',
        self::PROCESS_OCR_OUTPUT_FILE_ERROR => 'Rejected',
        self::UPLOAD_REQUESTED => 'Intake',
    ];

    public $table = 't_job_state_changes';

    public $fillable = [
        'request_id',
        'status_date',
        'status',
        'status_metadata',
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

    public function ocrRequest()
    {
        return $this->hasOne(OCRRequest::class, 't_job_state_changes_id');
    }

    public function getDisplayStatusAttribute()
    {
        return self::STATUS_MAP[$this->status] ?? '-';
    }
}
