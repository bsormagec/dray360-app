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

    public function ocrRequests()
    {
        return $this->belongsTo(OCRRequest::class, 'request_id', 'request_id');
    }

    public function ocrRequest()
    {
        return $this->hasOne(OCRRequest::class, 't_job_state_changes_id');
    }
}
