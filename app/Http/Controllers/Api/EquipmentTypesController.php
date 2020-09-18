<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\TMSProvider;
use App\Models\EquipmentType;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentTypesController extends Controller
{
    public function __invoke(Company $company, TMSProvider $tmsProvider)
    {
        $query = EquipmentType::query()
            ->where([
                't_company_id' => $company->id,
                't_tms_provider_id' => $tmsProvider->id,
            ]);

        $equipmentTypes = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('query', 'equipment_display'),
            ])
            ->defaultSort('equipment_display')
            ->allowedSorts(['equipment_display'])
            ->get();

        return JsonResource::collection($equipmentTypes);
    }
}
