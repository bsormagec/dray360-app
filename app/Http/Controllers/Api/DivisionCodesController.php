<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\TMSProvider;
use App\Models\DivisionCode;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Http\Resources\Json\JsonResource;

class DivisionCodesController extends Controller
{
    public function __invoke(Company $company, TMSProvider $tmsProvider)
    {
        $query = DivisionCode::forCompanyAndTmsProvider($company->id, $tmsProvider->id);

        $divisionCodes = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('name', 'division_name'),
            ])
            ->defaultSort('division_name')
            ->allowedSorts([
                AllowedSort::field('name', 'division_name'),
            ])
            ->get();

        return JsonResource::collection($divisionCodes);
    }
}
