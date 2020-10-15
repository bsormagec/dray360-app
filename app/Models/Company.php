<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\EncryptsAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property \App\Models\Address $address
 * @property \Illuminate\Database\Eloquent\Collection $companyAddressTmsCodes
 * @property \Illuminate\Database\Eloquent\Collection $contacts
 * @property \App\Models\Domain $domain
 * @property integer $t_address_id
 * @property string $name
 */
class Company extends Model
{
    use SoftDeletes;
    use EncryptsAttributes;

    const CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at',
        FOREIGN_KEY = 't_company_id',
        CUSHING = 'Cushing',
        TCOMPANIES_DEMO = 'TCompaniesDemo',
        POLARIS = 'Polaris',
        IXT_ONBOARDING = 'IXTOnboarding',
        IXT = 'IXT';

    public $table = 't_companies';

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'blackfly_token',
        'ripcms_password',
    ];

    public $fillable = [
        't_address_id',
        'name',
        'email_intake_address',
        'email_intake_address_alt',
        'default_tms_provider_id',
        'refs_custom_mapping',
        'configuration',
        'automatic_address_verification_threshold',
        't_domain_id',
        'blackfly_token',
        'blackfly_imagetype',
        'ripcms_username',
        'ripcms_password',
    ];

    protected $encryptable = [
        'blackfly_token',
        'ripcms_password',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'id' => 'integer',
        't_address_id' => 'integer',
        'automatic_address_verification_threshold' => 'integer',
        'name' => 'string',
        'refs_custom_mapping' => 'json',
        'configuration' => 'json',
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        't_address_id' => 'required',
        'refs_custom_mapping' => 'required'
    ];

    public function address()
    {
        return $this->belongsTo(\App\Models\AAddress::class, 't_address_id');
    }

    public function companyAddressTmsCodes()
    {
        return $this->hasMany(\App\Models\CompanyAddressTmsCode::class, 't_company_id');
    }

    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 't_company_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 't_domain_id');
    }

    /**
     * Get company 'Cushing'
     */
    public static function getCushing(): self
    {
        return static::where('name', static::CUSHING)->first();
    }

    /**
     * Get company 'TCompanies Demo'
     */
    public static function getTCompaniesDemo(): self
    {
        return static::where('name', static::TCOMPANIES_DEMO)->first();
    }

    /**
     * Get company 'IXT onboarding'
     */
    public static function getIxtOnboarding(): self
    {
        return static::where('name', static::IXT_ONBOARDING)->first();
    }

    /**
     * Get company 'IXT'
     */
    public static function getIxt(): self
    {
        return static::where('name', static::IXT)->first();
    }

    public function variantsAccessorials(): BelongsToMany
    {
        return $this->belongsToMany(
            OCRVariant::class,
            't_company_ocrvariant_accessorial_mappings',
            't_company_id',
            't_ocrvariant_id'
        )
        ->using(AccesorialMappingPivot::class)
        ->withPivot(['mapping']);
    }
}
