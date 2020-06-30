<?php

namespace App\Http\Controllers\Api;

use App\Models\OCRVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class OCRVariantsController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', OCRVariant::class);

        return JsonResource::collection(OCRVariant::paginate(25));
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
