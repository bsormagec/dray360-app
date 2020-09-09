<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\OCRVariant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccesorialCompaniesController extends Controller
{
    public function show(Company $company, OCRVariant $variant)
    {
        $this->authorize('viewAccesorialMapping', $company);
        $variantAccesorial = $company->variantsAccessorials()->where('t_ocrvariant_id', $variant->id)->first();
        return response()->json($variantAccesorial->pivot ?? []);
    }

    public function update(Request $request, Company $company, OCRVariant $variant)
    {
        $this->authorize('updateAccesorialMapping', $company);
        $data = $request->validate([
            'billing-mapping' => 'sometimes|array'
        ]);
        $mappingObject['mapping'] = $data['billing-mapping'];
        $company->variantsAccessorials()->syncWithoutDetaching([$variant->id => $mappingObject]);

        return response()->json([
            'data' => [
                't_company_id' => $company->id,
                't_ocrvariant_id' => $variant->id,
            ] + $data
        ]);
    }
}
