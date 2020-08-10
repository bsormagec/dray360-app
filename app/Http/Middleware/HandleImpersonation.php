<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Impersonation\ImpersonationManager;

class HandleImpersonation
{
    protected ImpersonationManager $impersonate;

    public function __construct(ImpersonationManager $impersonate)
    {
        $this->impersonate = $impersonate;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->impersonate->isImpersonating()) {
            $this->impersonate->loadForRequest();
        }

        return $next($request);
    }
}
