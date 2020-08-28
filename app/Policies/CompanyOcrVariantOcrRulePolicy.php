<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CompanyOCRVariantOCRRule;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyOcrVariantOcrRulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAbleTo('rules-editor-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CompanyOCRVariantOCRRule $companyOcrVariantOcrRule): bool
    {
        return $user->isAbleTo('rules-editor-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAbleTo('rules-editor-assign');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CompanyOCRVariantOCRRule $companyOcrVariantOcrRule): bool
    {
        return $user->isAbleTo('rules-editor-assign');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CompanyOCRVariantOCRRule $companyOcrVariantOcrRule): bool
    {
        return $user->isAbleTo('rules-editor-assign');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CompanyOCRVariantOCRRule $companyOcrVariantOcrRule): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CompanyOCRVariantOCRRule $companyOcrVariantOcrRule): bool
    {
        return false;
    }
}
