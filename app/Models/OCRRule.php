<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Database\Eloquent\Collection $companiesVariantsAssignment
 * @property string $name
 * @property string $description
 * @property string $code
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class OCRRule extends Model
{
    use SoftDeletes;

    public $table = 't_ocrrules';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'description',
        'code'
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        'name' => 'required',
        'description' => 'required',
        'code' => 'required'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleted(function ($ocrRule) {
            $ocrRule->companiesVariantsAssignment()->delete();
        });
    }

    /**
     * Relationship with the assigment of the rules to company in given variants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companiesVariantsAssignment()
    {
        return $this->hasMany(CompanyOCRVariantOCRRule::class, 't_ocrrule_id');
    }
}
