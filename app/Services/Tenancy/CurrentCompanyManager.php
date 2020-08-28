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
    protected ?Company $company = null;

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
            $this->setCompany($auth->user()->company()->first());
        }

        return $this;
    }

    protected function getCompanyFromRequestParams(Request $request): self
    {
        $companyId = $request->get(self::REQUEST_PARAMETER);
        if (! $companyId || ! is_numeric($companyId)) {
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
        if (! $companyId || ($this->company && ! is_superadmin())) {
            return;
        }

        $this->setCompanyFromId($companyId);
    }

    public function setCompanyFromId(?int $id)
    {
        $company = Company::find($id);

        if (! $company) {
            return;
        }

        $this->setCompany($company);
    }

    public function setCompany(?Company $company)
    {
        $this->company = $company;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function isCompanySet(): bool
    {
        return $this->company != null;
    }

    public function companyId(): ?int
    {
        return $this->company->id ?? null;
    }
}
