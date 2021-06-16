<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAbleTo('companies-view');
    }

    public function create(User $user): bool
    {
        return $user->isAbleTo('companies-create');
    }

    public function view(User $user): bool
    {
        return $user->isAbleTo('companies-view');
    }

    public function update(User $user, Company $company): bool
    {
        if (request()->has('refs_custom_mapping')) {
            return $user->isAbleTo('refs-custom-mapping-edit');
        }

        return $user->isAbleTo('companies-edit');
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->isAbleTo('companies-remove');
    }
}
