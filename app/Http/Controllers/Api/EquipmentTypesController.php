<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\TMSProvider;
use App\Models\EquipmentType;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentTypesController extends Controller
{
    public function __invoke(Company $company, TMSProvider $tmsProvider)
    {
        $query = EquipmentType::forCompanyAndTmsProvider($company->id, $tmsProvider->id);

        $equipmentTypes = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('display', 'equipment_display'),
                AllowedFilter::partial('type_and_size', 'equipment_type_and_size'),
                AllowedFilter::partial('type', 'equipment_type'),
                AllowedFilter::partial('size', 'equipment_size'),
                AllowedFilter::partial('owner', 'equipment_owner'),
                AllowedFilter::partial('scac', 'scac'),
                AllowedFilter::partial('prefix', 'line_prefix_list'),
            ])
            ->defaultSort('equipment_display')
            ->allowedSorts([    
                AllowedSort::field('display', 'equipment_display'),
                AllowedSort::field('type_and_size', 'equipment_type_and_size'),
                AllowedSort::field('type', 'equipment_type'),
                AllowedSort::field('size', 'equipment_size'),
                AllowedSort::field('size', 'equipment_size'),
                AllowedSort::field('owner', 'equipment_owner'),
                AllowedSort::field('scac', 'scac'),
                AllowedSort::field('prefix', 'line_prefix_list'),
            ])
            ->get();

        return JsonResource::collection($equipmentTypes);
    }
}
