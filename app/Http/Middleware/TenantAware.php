<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

class TenantAware
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->app['impersonate']->isImpersonating()) {
            return $next($request);
        }

        $this->app['tenancy']->initialize($request);

        if (
            $this->app['auth']->guest()
            || $this->app->runningUnitTests()
            || $this->app['tenancy']->isUsingRightDomain($request, $this->app['auth']->user())
        ) {
            return $next($request);
        }

        return $this->app['tenancy']->getRedirectErrorResponse($this->app['auth']->user());
    }
}
