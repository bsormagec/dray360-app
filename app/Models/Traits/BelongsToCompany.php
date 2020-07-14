<?php

namespace App\Models\Traits;

use App\Models\Company;
use App\Contracts\CurrentCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    public function scopeForCurrentCompany($query, CurrentCompany $company = null)
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
}
