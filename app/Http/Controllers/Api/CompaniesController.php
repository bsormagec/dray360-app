<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class CompaniesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:companies-view')->only('index');
    }

    public function index()
    {
        return JsonResource::collection(
            Company::query()
                ->active()
                ->orderBy('name')
                ->get()
        );
    }

    public function update(Request $request, Company $company)
    {
        $this->authorize('update', $company);
        $data = $request->validate([
            'refs_custom_mapping' => 'sometimes|array',
            't_address_id' => 'sometimes|int',
            'name' => 'sometimes|string',
            'email_intake_address' => 'sometimes|string',
            'email_intake_address_alt' => 'sometimes|string'
        ]);

        $company->update($data);

        return response()->json(['data' => $company]);
    }

    /**
    * Display the specified resource.
    */
    public function show(Company $company)
    {
        $this->authorize('view', $company);
        return response()->json($company);
    }
}
