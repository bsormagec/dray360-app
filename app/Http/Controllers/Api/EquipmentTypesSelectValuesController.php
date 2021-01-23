<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\TMSProvider;
use App\Models\EquipmentType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EquipmentTypesSelectValuesController extends Controller
{
    public function __invoke(Company $company, TMSProvider $tmsProvider)
    {
        return response()->json([
            'equipment_types' => EquipmentType::query()
                ->forCompanyAndTmsProvider($company->id, $tmsProvider->id)
                ->select(DB::raw('distinct equipment_type'))
                ->orderby('equipment_type')
                ->get()
                ->pluck('equipment_type'),
            'equipment_owners' => EquipmentType::query()
                ->forCompanyAndTmsProvider($company->id, $tmsProvider->id)
                ->select(DB::raw('distinct equipment_owner'))
                ->orderby('equipment_owner')
                ->get()
                ->pluck('equipment_owner'),
            'equipment_sizes' => EquipmentType::query()
                ->forCompanyAndTmsProvider($company->id, $tmsProvider->id)
                ->select(DB::raw('distinct equipment_size'))
                ->orderby('equipment_size')
                ->get()
                ->pluck('equipment_size'),
            'scacs' => EquipmentType::query()
                ->forCompanyAndTmsProvider($company->id, $tmsProvider->id)
                ->select(DB::raw('distinct scac'))
                ->orderby('scac')
                ->get()
                ->pluck('scac'),
            'prefix_list' => EquipmentType::query()
                ->forCompanyAndTmsProvider($company->id, $tmsProvider->id)
                ->select(DB::raw('distinct line_prefix_list'))
                ->get()
                ->pluck('line_prefix_list') 
                ->flatten()
                ->unique()
                ->sort()
                ->values(),   
            'equipment_types_and_sizes' => EquipmentType::query()
                ->forCompanyAndTmsProvider($company->id, $tmsProvider->id)
                ->select(DB::raw('distinct equipment_type_and_size'))
                ->orderby('equipment_type_and_size')
                ->get()
                ->pluck('equipment_type_and_size'),
        ]);
    }
}
