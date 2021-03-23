<?php

namespace App\Models;

use App\Models\Traits\HasLocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Database\Eloquent\Collection $orders
 * @property \Illuminate\Database\Eloquent\Collection $statusList
 * @property \App\Models\OCRRequestStatus $latestOcrRequestStatus
 * @property string $request_id
 * @property string $t_job_state_changes_id
 * @property \Carbon\Carbon $done_at intake_date
 * @property \Carbon\Carbon $deleted_at intake_date
 * @property \Carbon\Carbon $created_at intake_date
 * @property \Carbon\Carbon $updated_at latest_status_date
 */
class OCRRequest extends Model
{
    use SoftDeletes;
    use HasLocks;

    public $table = 't_job_latest_state';

    public $fillable = [
        'request_id',
        't_job_state_changes_id',
        'order_id',
        'done_at',
    ];

    protected $casts = [
        'done_at' => 'datetime',
    ];

    protected $objectLockType = ObjectLock::REQUEST_OBJECT;
    protected $objectLockLocalKey = 'request_id';

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
        return $this->belongsTo(OCRRequestStatus::class, 't_job_state_changes_id');
    }
}
