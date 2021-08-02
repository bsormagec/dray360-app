<?php

namespace App\Models;

use stdClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\EncryptsAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Address $address
 * @property \Illuminate\Database\Eloquent\Collection $companyAddressTmsCodes
 * @property \App\Models\Domain $domain
 * @property int $t_address_id
 * @property array $configuration
 * @property string email_intake_address
 * @property string email_intake_address_alt
 * @property int default_tms_provider_id
 * @property array refs_custom_mapping
 * @property array configuration
 * @property array company_config
 * @property int automatic_address_verification_threshold
 * @property int t_domain_id
 * @property string blackfly_token
 * @property string blackfly_imagetype
 * @property string ripcms_username
 * @property string ripcms_password
 * @property string compcare_api_key
 * @property string chainio_url
 * @property string chainio_api_key
 * @property boolean sync_addresses
 * @property string $name
 */
class Company extends Model
{
    use SoftDeletes;
    use EncryptsAttributes;

    const FOREIGN_KEY = 't_company_id',
        TCOMPANIES_DEMO_ID = 2;

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
        'email_onboarding_address',
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
        'compcare_api_key',
        'chainio_url',
        'chainio_api_key',
        'sync_addresses',
        't_fieldmap_id',
    ];

    protected $encryptable = [
        'blackfly_token',
        'ripcms_password',
        'compcare_api_key',
        'chainio_api_key',
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

    protected static function booted()
    {
        static::creating(function ($company) {
            $env = config('app.env') === 'production' ? 'prod' : 'dev';
            $companyName = Str::snake($company->name, '');
            $uuid = str_replace('-', '', Str::uuid()->toString());
            $company->email_intake_address = "{$env}+{$companyName}_{$uuid}@in.dray360.com";

            $uuid = str_replace('-', '', Str::uuid()->toString());
            $company->email_onboarding_address = "{$env}+{$companyName}_onboarding_{$uuid}@in.dray360.com";

            if (empty($company->configuration)) {
                $company->configuration = new stdClass();
            }
        });

        static::saving(function ($company) {
            if (empty($company->configuration)) {
                $company->configuration = new stdClass();
            }
        });
    }

    public function address()
    {
        return $this->belongsTo(\App\Models\AAddress::class, 't_address_id');
    }

    public function companyAddressTmsCodes()
    {
        return $this->hasMany(\App\Models\CompanyAddressTmsCode::class, 't_company_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 't_domain_id');
    }

    public function defaultTmsProvider()
    {
        return $this->belongsTo(TMSProvider::class, 'default_tms_provider_id');
    }

    public function fieldMap()
    {
        return $this->belongsTo(FieldMap::class, 't_fieldmap_id');
    }

    public static function withTemplates()
    {
        return static::query()
            ->select([
                'id',
                DB::raw("json_extract(configuration, '$.profit_tools_template_list') as profit_tools_template_list")
            ])
            ->where('configuration->profit_tools_enable_templates', true)
            ->get();
    }
}
