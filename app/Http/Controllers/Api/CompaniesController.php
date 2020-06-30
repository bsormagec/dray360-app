<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
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
        return JsonResource::collection(Company::all());
    }
}
