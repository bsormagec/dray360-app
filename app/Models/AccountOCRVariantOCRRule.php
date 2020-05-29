<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'rule_sequence',
        'created_at',
        'updated_at',
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
    public function ocrRule()
    {
        return $this->belongsTo(OCRRule::class, 't_ocrrule_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function ocrVariant()
    {
        return $this->belongsTo(OCRVariant::class, 't_ocrvariant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function account()
    {
        return $this->belongsTo(Account::class, 't_account_id');
    }

    /**
     * Get the assignments assigned to the given account and variant.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param integer $accountId
     * @param integer $variantId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedTo(Builder $query, int $accountId, int $variantId)
    {
        return $query->where([
            't_account_id' => $accountId,
            't_ocrvariant_id' => $variantId,
        ]);
    }
}
