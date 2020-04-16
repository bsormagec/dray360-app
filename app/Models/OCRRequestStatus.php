<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OCRRequestStatus
 * @package App\Models
 * @version April 9, 2020, 8:00 pm UTC
 *
 * @property boolean is_latest_status
 * @property string request_id
 * @property string|\Carbon\Carbon status_date_utc
 * @property string|\Carbon\Carbon status_date_cst
 * @property string status
 * @property longtext status_summary
 * @property json status_metadata

 * @property Illuminate\Database\Eloquent\Collection ocrRequest
 */
class OCRRequestStatus extends Model
{
    public $table = 'v_status_summary';

    protected $casts = [
        'status_metadata' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    **/
    public function ocrRequest()
    {
        return $this->belongsTo('App\Models\OCRRequest','request_id','request_id');
    }

}
