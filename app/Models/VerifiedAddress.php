<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifiedAddress extends Model
{
    use SoftDeletes;

    public $table = 't_verified_addresses';
    protected $dates = ['deleted_at'];

    public $fillable = [
        't_company_id',
        't_tms_provider_id',
        'ocr_address_raw_text',
        'company_address_tms_code',
        'company_address_tms_text',
        'verified_count',
        'skip_verification',
        'deleted_reason',
    ];

    protected $casts = [
        'skip_verification' => 'boolean',
        'verified_count' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 't_company_id');
    }

    public function tmsProvider()
    {
        return $this->belongsTo(TMSProvider::class, 't_tms_provider_id');
    }
}
