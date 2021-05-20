<?php

namespace App\Http\Controllers\Api;

use App\Models\OCRVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Http\Resources\Json\JsonResource;

class OCRVariantsController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', OCRVariant::class);

        $ocrVariants = QueryBuilder::for(OCRVariant::class)
            ->allowedFields((new OCRVariant())->getFillable())
            ->allowedFilters([
                'abbyy_variant_name',
                'description',
                'variant_type',
                AllowedFilter::callback('company_id', function ($query, $value) {
                    $query->whereJsonContains('company_id_list', intval($value));
                }),
            ])
            ->allowedSorts([
                'abbyy_variant_name',
                'abbyy_variant_id',
                'description',
            ])
            ->paginate(2500);

        return JsonResource::collection($ocrVariants);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', OCRVariant::class);
        $data = $request->validate(OCRVariant::$rules);

        $ocrVariant = OCRVariant::create($data);

        return response()->json($ocrVariant, Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OCRVariant  $oCRVariant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OCRVariant $ocrVariant)
    {
        $this->authorize('update', $ocrVariant);
        $data = $request->validate(OCRVariant::$rules);

        $ocrVariant->update($data);

        return response()->json($ocrVariant);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OCRVariant  $oCRVariant
     * @return \Illuminate\Http\Response
     */
    public function destroy(OCRVariant $ocrVariant)
    {
        $this->authorize('delete', $ocrVariant);
        $ocrVariant->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
