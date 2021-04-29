<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class FieldMap extends Model
{
    public $timestamps = false;

    public $table = 't_fieldmaps';

    public $fillable = [
        'system_default',
        'fieldmap_config',
        'created_at',
        'replaced_at',
        'replacedby_id',
        'replaces_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'replaced_at' => 'datetime',
        'system_default' => 'bool',
        'fieldmap_config' => 'json',
    ];

    public function company()
    {
        return $this->hasOne(Company::class, 't_fieldmap_id');
    }

    public function tmsProvider()
    {
        return $this->hasOne(TMSProvider::class, 't_fieldmap_id');
    }

    public function ocrVariant()
    {
        return $this->hasOne(OCRVariant::class, 't_fieldmap_id');
    }

    public function companyOcrVariant()
    {
        return $this->hasOne(CompanyOcrVariant::class, 't_fieldmap_id');
    }

    public static function getFrom(array $params, bool $useCompanyVariantCombined = true): array
    {
        $companyId = $params['company_id'] ?? null;
        $tmsProviderId = $params['tms_provider_id'] ?? null;

        if ($companyId) {
            $company = Company::find($companyId, ['id', 'default_tms_provider_id']);
            $tmsProviderId = $company->default_tms_provider_id;
        }

        $response = DB::selectOne("
            select json_pretty(json_merge_patch(
                coalesce(( select f.fieldmap_config from t_fieldmaps as f                                                          where f.id = (select id from t_fieldmaps as f where f.system_default and f.replaced_at is null order by f.id desc limit 1) ), '{}')
                ,coalesce(( select f.fieldmap_config from t_fieldmaps as f join t_tms_providers      as t on t.t_fieldmap_id = f.id where t.id = ? ), '{}')
                ,coalesce(( select f.fieldmap_config from t_fieldmaps as f join t_ocrvariants        as c on c.t_fieldmap_id = f.id where c.id = ? ), '{}')
                ,coalesce(( select f.fieldmap_config from t_fieldmaps as f join t_companies          as c on c.t_fieldmap_id = f.id where c.id = ? ), '{}')
                ,coalesce(( select f.fieldmap_config from t_fieldmaps as f join t_company_ocrvariant as v on v.t_fieldmap_id = f.id where v.id = (select id from t_company_ocrvariant where t_ocrvariant_id = ? and t_company_id = ?) ), '{}')
            )) as fieldmap
        ", [
            $tmsProviderId,
            $params['variant_id'] ?? null,
            $companyId,
            $useCompanyVariantCombined ? ($params['variant_id'] ?? null) : null,
            $useCompanyVariantCombined ? $companyId : null,
        ]);

        return json_decode($response->fieldmap, true);
    }

    public static function createFrom(array $params, FieldMap $replaces = null): self
    {
        $mapFromPreviousLevel = [];
        if (isset($params['variant_id']) && isset($params['company_id'])) {
            $mapFromPreviousLevel = static::getFrom([
                'company_id' => $params['company_id'],
                'variant_id' => $params['variant_id'],
            ], false);
        } elseif (isset($params['company_id'])) {
            $company = Company::find($params['company_id'], ['id', 'default_tms_provider_id']);
            $mapFromPreviousLevel = static::getFrom(['tms_provider_id' => $company->default_tms_provider_id]);
        } elseif (isset($params['variant_id']) || isset($params['tms_provider_id'])) {
            $mapFromPreviousLevel = static::getFrom([]);
        }

        $mapDiff = array_diff_assoc_recursive($params['fieldmap_config'], $mapFromPreviousLevel);

        $data = [
            'fieldmap_config' => empty($mapDiff) ? new \stdClass() : $mapDiff,
            'created_at' => now(),
            'replaces_id' => $replaces ? $replaces->id : null,
        ];

        $newModel = static::create($data);

        if ($replaces) {
            $replaces->update([
                'replaced_at' => now(),
                'replacedby_id' => $newModel->id,
            ]);
        }

        return $newModel;
    }
}
