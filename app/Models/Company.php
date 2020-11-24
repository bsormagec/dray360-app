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

    const FOREIGN_KEY = 't_company_id';

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
        'company_config',
        'automatic_address_verification_threshold',
        't_domain_id',
        'blackfly_token',
        'blackfly_imagetype',
        'ripcms_username',
        'ripcms_password',
        'compcare_organization_id',
        'compcare_username',
        'compcare_password',
        'sync_addresses',
    ];

    protected $encryptable = [
        'blackfly_token',
        'ripcms_password',
        'compcare_password',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'id' => 'integer',
        't_address_id' => 'integer',
        'automatic_address_verification_threshold' => 'integer',
        'sync_addresses' => 'boolean',
        'name' => 'string',
        'refs_custom_mapping' => 'json',
        'configuration' => 'json',
        'company_config' => 'json',
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

    public function defaultTmsProvider()
    {
        return $this->belongsTo(TMSProvider::class, 'default_tms_provider_id');
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
