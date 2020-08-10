<?php

namespace App\Services\Tenancy;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;

class CurrentCompanyManager
{
    const REQUEST_PARAMETER = 'company_id',
        HEADER = 'X-Company';
    protected Application $app;
    protected ?Company $currentCompany = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function initialize(Request $request)
    {
        $this->discoverCompanyFromUser()
            ->getCompanyFromRequestParams($request)
            ->getCompanyFromHeader($request);
    }

    protected function discoverCompanyFromUser(): self
    {
        $auth = $this->app['auth'];
        if (! $auth->guest() && $auth->user()->hasCompany()) {
            $this->setCurrentCompany($auth->user()->company()->first());
        }

        return $this;
    }

    protected function getCompanyFromRequestParams(Request $request): self
    {
        if (! $request->has(self::REQUEST_PARAMETER)) {
            return $this;
        }

        $this->setOrReplaceFromId($request->get(self::REQUEST_PARAMETER));

        return $this;
    }

    protected function getCompanyFromHeader(Request $request): self
    {
        if (! $request->hasHeader(self::HEADER)) {
            return $this;
        }

        $this->setOrReplaceFromId($request->header(self::HEADER));

        return $this;
    }

    protected function setOrReplaceFromId(?int $companyId)
    {
        if (! $companyId || ($this->currentCompany && ! is_superadmin())) {
            return;
        }

        $this->setCurrentCompanyFromId($companyId);
    }

    public function setCurrentCompanyFromId(?int $id)
    {
        $company = Company::find($id);

        if (! $company) {
            return;
        }

        $this->setCurrentCompany($company);
    }

    public function setCurrentCompany(?Company $company)
    {
        $this->currentCompany = $company;
    }

    public function getCurrentCompany(): ?Company
    {
        return $this->currentCompany;
    }

    public function isSetCurrentCompany(): bool
    {
        return $this->currentCompany != null;
    }

    public function currentCompanyId(): ?int
    {
        return $this->currentCompany->id ?? null;
    }
}
