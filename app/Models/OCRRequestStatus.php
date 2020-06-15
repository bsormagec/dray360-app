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
    const STATUS_MAP = [
        'intake-accepted' => 'Processing',
        'intake-exception' => 'Exception',
        'intake-rejected' => 'Rejected',
        'intake-started' => 'Intake',
        'ocr-completed' => 'Processing',
        'ocr-post-processing-complete' => 'Verified',
        'ocr-post-processing-error' => 'Rejected',
        'ocr-waiting' => 'Processing',
        'process-ocr-output-file-complete' => 'Processing',
        'process-ocr-output-file-error' => 'Rejected',
        'upload-requested' => 'Intake',
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
