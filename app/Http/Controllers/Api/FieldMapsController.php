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

        return JsonResource::make(
            FieldMap::getFrom($params)
        );
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
            'company_id' => 'required_without_all:variant_id,tms_provider_id|exists:t_companies,id',
            'variant_id' => 'required_without_all:company_id,tms_provider_id|exists:t_ocrvariants,id',
            'tms_provider_id' => 'required_without_all:company_id,variant_id|exists:t_tms_providers,id',
            'fieldmap_config' => 'required|array'
        ]);

        $relatedObject = null;
        if (isset($data['variant_id']) && isset($data['company_id'])) {
            $relatedObject = CompanyOcrVariant::query()
                ->with('fieldMap:id')
                ->firstOrNew([
                    't_company_id' => $data['company_id'],
                    't_ocrvariant_id' => $data['variant_id'],
                ]);
        } elseif (isset($data['company_id'])) {
            $relatedObject = Company::query()
                ->with('fieldMap:id')
                ->findOrFail($data['company_id'], ['id', 't_fieldmap_id']);
        } elseif (isset($data['variant_id'])) {
            $relatedObject = OCRVariant::query()
                ->with('fieldMap:id')
                ->findOrFail($data['variant_id'], ['id', 't_fieldmap_id']);
        } elseif (isset($data['tms_provider_id'])) {
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
