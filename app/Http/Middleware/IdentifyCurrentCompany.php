<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IdentifyCurrentCompany
{
    public function handle(Request $request, Closure $next)
    {
        app('company_manager')->initialize($request);

        return $next($request);
    }
}
