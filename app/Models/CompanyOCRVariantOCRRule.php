<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyOCRVariantOCRRule extends Model
{
    use SoftDeletes;

    public $table = 't_company_ocrvariant_ocrrules';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];
    public $fillable = [
        't_company_id',
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
    protected $casts = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        't_company_id' => 'required',
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
    public function company()
    {
        return $this->belongsTo(Company::class, 't_company_id');
    }

    public function scopeAssignedTo(Builder $query, int $companyId, int $variantId): Builder
    {
        return $query->where([
            't_company_id' => $companyId,
            't_ocrvariant_id' => $variantId,
        ]);
    }
}
