<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Contracts\CurrentCompany;
use Illuminate\Contracts\Foundation\Application;

class IdentifyCurrentCompany
{
    const REQUEST_PARAMETER = 'company_id',
        HEADER = 'X-Company';
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->discoverCompanyFromUser()
            ->getCompanyFromRequestParams($request)
            ->getCompanyFromHeader($request);

        return $next($request);
    }

    protected function discoverCompanyFromUser(): self
    {
        if (! auth()->guest() && auth()->user()->hasCompany()) {
            currentCompany(auth()->user()->company()->first());
        }

        return $this;
    }

    protected function getCompanyFromRequestParams(Request $request): self
    {
        if (! $request->has(self::REQUEST_PARAMETER)) {
            return $this;
        }

        if ($this->app->bound(CurrentCompany::class) && ! is_superadmin()) {
            return $this;
        }

        $company = Company::find($request->get(self::REQUEST_PARAMETER));

        if ($company) {
            currentCompany($company);
        }

        return $this;
    }

    protected function getCompanyFromHeader(Request $request): self
    {
        if (! $request->hasHeader(self::HEADER)) {
            return $this;
        }

        if ($this->app->bound(CurrentCompany::class) && ! is_superadmin()) {
            return $this;
        }

        $company = Company::find($request->header(self::HEADER));

        if ($company) {
            currentCompany($company);
        }

        return $this;
    }
}
