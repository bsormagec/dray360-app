<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Database\Eloquent\Collection $orders
 * @property \Illuminate\Database\Eloquent\Collection $statusList
 * @property \App\Models\OCRRequestStatus $latestOcrRequestStatus
 * @property string $request_id
 * @property string $t_job_state_changes_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class OCRRequest extends Model
{
    public $table = 't_job_latest_state';

    protected $casts = [
    ];

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'request_id', 'request_id');
    }

    public function statusList()
    {
        return $this->hasMany(OCRRequestStatus::class, 'request_id', 'request_id');
    }

    public function latestOcrRequestStatus()
    {
        return $this->hasOne(OCRRequestStatus::class, 'request_id', 'request_id')
            ->where('is_latest_status', true);
    }
}
