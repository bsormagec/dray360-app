<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountOCRVariantOCRRule extends Model
{
    use SoftDeletes;

    public $table = 't_account_ocrvariant_ocrrules';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];



    public $fillable = [
        't_account_id',
        't_ocrvariant_id',
        't_ocrrule_id',
        'rule_sequence'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        't_account_id' => 'integer',
        't_ocrvariant_id' => 'integer',
        't_ocrrule_id' => 'integer',
        'rule_sequence' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        't_account_id' => 'required',
        't_ocrvariant_id' => 'required',
        't_ocrrule_id' => 'required',
        'sequence_number' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function ocrrule()
    {
        return $this->belongsTo(\App\Models\OCRRule::class, 't_ocrrule_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function order()
    {
        return $this->belongsTo(\App\Models\OCRVariant::class, 't_ocrvariant_id');
    }
}
