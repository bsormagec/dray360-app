<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrentTenantController extends Controller
{
    public function __invoke(Request $request)
    {
        /** @var \App\Services\Tenancy\TenancyManager */
        $tenancy = app('tenancy');

        return response()->json(
            $tenancy->getConfiguration(auth()->user())
        );
    }
}
