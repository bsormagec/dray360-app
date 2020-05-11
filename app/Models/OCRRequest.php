<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OCRRequest
 * @package App\Models
 * @version April 9, 2020, 8:00 pm UTC
 *
 * @property string request_id
 * @property string|\Carbon\Carbon created_at
 * @property string|\Carbon\Carbon updated_at
 *
 * @property \Illuminate\Database\Eloquent\Collection orders
 * @property \Illuminate\Database\Eloquent\Collection statusList
 */
class OCRRequest extends Model
{
    public $table = 't_job_latest_state';

    protected $casts = [
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'request_id', 'request_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function statusList()
    {
        return $this->hasMany(\App\Models\OCRRequestStatus::class, 'request_id', 'request_id');
    }

}
