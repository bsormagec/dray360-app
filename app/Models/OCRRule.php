<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static \Illuminate\Database\Eloquent\Builder filterByAccountVariant(\Illuminate\Http\Request $request)
 */
class OCRRule extends Model
{
    use SoftDeletes;

    public $table = 't_ocrrules';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'description',
        'code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'description' => 'required',
        'code' => 'required'
    ];

    /**
     * Relationship with the assigment of the rules to account in given variants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accountsVariantsAssignment()
    {
        return $this->hasMany(AccountOCRVariantOCRRule::class, 't_ocrrule_id');
    }

    /**
     * Scope to filter the rules by the given account and variant.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByAccountVariant(Builder $query, array $filters)
    {
        if (!isset($filters['account_id']) || !isset($filters['variant_id'])) {
            return $query;
        }

        return $query->whereHas('accountsVariantsAssignment', function ($query) use ($filters) {
            $query->where([
                't_account_id' => $filters['account_id'],
                't_ocrvariant_id' => $filters['variant_id'],
            ]);
        });
    }
}
