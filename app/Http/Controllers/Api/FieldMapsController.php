<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\FieldMap;
use App\Models\OCRVariant;
use App\Models\TMSProvider;
use Illuminate\Http\Request;
use App\Models\CompanyOcrVariant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class FieldMapsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', FieldMap::class);

        $params = $request->validate([
            'company_id' => 'sometimes|nullable|exists:t_companies,id',
            'variant_id' => 'sometimes|nullable|exists:t_ocrvariants,id',
            'tms_provider_id' => 'sometimes|nullable|exists:t_tms_providers,id',
        ], ['all' => true]);

        return JsonResource::make([
            'current' => FieldMap::getFrom($params),
            'previous' => FieldMap::getPreviousLevelFrom($params),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', FieldMap::class);
        $data = $request->validate([
            'company_id' => 'sometimes|nullable|exists:t_companies,id',
            'variant_id' => 'sometimes|nullable|exists:t_ocrvariants,id',
            'tms_provider_id' => 'sometimes|nullable|exists:t_tms_providers,id',
            'fieldmap_config' => 'required|array'
        ]);

        $relatedObject = null;
        $companyId = $data['company_id'] ?? null;
        $variantId = $data['variant_id'] ?? null;
        $tmsProviderId = $data['tms_provider_id'] ?? null;

        if (! $companyId && ! $variantId && ! $tmsProviderId) {
            $defaultFieldMap = FieldMap::getSystemDefault();
            $newDefaultFieldMap = FieldMap::createFrom($data, $defaultFieldMap);
            return $newDefaultFieldMap;
        }

        if ($variantId && $companyId) {
            $relatedObject = CompanyOcrVariant::query()
                ->with('fieldMap:id')
                ->firstOrNew([
                    't_company_id' => $data['company_id'],
                    't_ocrvariant_id' => $data['variant_id'],
                ]);
        } elseif ($companyId) {
            $relatedObject = Company::query()
                ->with('fieldMap:id')
                ->findOrFail($data['company_id'], ['id', 't_fieldmap_id']);
        } elseif ($variantId) {
            $relatedObject = OCRVariant::query()
                ->with('fieldMap:id')
                ->findOrFail($data['variant_id'], ['id', 't_fieldmap_id']);
        } elseif ($tmsProviderId) {
            $relatedObject = TMSProvider::query()
                ->with('fieldMap:id')
                ->findOrFail($data['tms_provider_id'], ['id', 't_fieldmap_id']);
        }

        $newFieldMap = FieldMap::createFrom($data, $relatedObject->fieldMap);
        $relatedObject->fieldMap()->associate($newFieldMap);
        $relatedObject->save();

        return $newFieldMap;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FieldMap  $fieldMap
     * @return \Illuminate\Http\Response
     */
    public function show(FieldMap $fieldMap)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FieldMap  $fieldMap
     * @return \Illuminate\Http\Response
     */
    public function edit(FieldMap $fieldMap)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FieldMap  $fieldMap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FieldMap $fieldMap)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FieldMap  $fieldMap
     * @return \Illuminate\Http\Response
     */
    public function destroy(FieldMap $fieldMap)
    {
    }
}
