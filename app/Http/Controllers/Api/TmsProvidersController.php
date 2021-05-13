<?php

namespace App\Http\Controllers\Api;

use App\Models\TMSProvider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Resources\Json\JsonResource;

class TmsProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TMSProvider::class);

        $tmsProviders = QueryBuilder::for(TMSProvider::class)
            ->allowedFilters(['name', 'id'])
            ->allowedSorts(['name'])
            ->paginate($request->get('perPage', 10));

        return JsonResource::collection($tmsProviders);
    }
}
