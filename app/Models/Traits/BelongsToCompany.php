<?php

namespace App\Models\Traits;

use Exception;
use ReflectionClass;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    public function scopeForCurrentCompany($query, Company $company = null)
    {
        $company = $company ?: currentCompany();

        if (! $company) {
            return $query;
        }

        return $query->where(Company::FOREIGN_KEY, $company->id);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, Company::FOREIGN_KEY);
    }

    public function hasCompany(): bool
    {
        return $this->getAttribute(Company::FOREIGN_KEY) !== null;
    }

    public function belongsToSameCompanyAs(Model $model): bool
    {
        $reflection = new ReflectionClass($model);

        if (! $reflection->hasMethod('hasCompany') || ! $reflection->hasMethod('getCompanyId')) {
            throw new Exception('The model '.get_class($model).' does not belong to a company.');
        }

        return $this->hasCompany()
            && $model->hasCompany()
            && $model->getCompanyId() == $this->getCompanyId();
    }

    public function getCompanyId(): ?int
    {
        return $this->getAttribute(Company::FOREIGN_KEY);
    }

    public function setCompany(Company $company)
    {
        $this->company()->associate($company);

        return $this;
    }
}
