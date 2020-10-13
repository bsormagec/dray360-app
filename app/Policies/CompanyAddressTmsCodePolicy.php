<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CompanyAddressTMSCode ;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyAddressTmsCodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isAbleTo('company-address-tms-code-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CompanyAddressTMSCode $companyAddressTMSCode)
    {
        return $user->isAbleTo('company-address-tms-code-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isAbleTo('company-address-tms-code-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CompanyAddressTMSCode $companyAddressTMSCode)
    {
        return $user->isAbleTo('company-address-tms-code-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CompanyAddressTMSCode $companyAddressTMSCode)
    {
        return $user->isAbleTo('company-address-tms-code-remove');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CompanyAddressTMSCode $companyAddressTMSCode)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CompanyAddressTMSCode $companyAddressTMSCode)
    {
        return false;
    }
}
