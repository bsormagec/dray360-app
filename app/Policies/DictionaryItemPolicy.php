<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DictionaryItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class DictionaryItemPolicy
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
        $canViewAllCompanies = $user->isAbleTo('all-companies-view');

        if (! $canViewAllCompanies && request()->has('filter.company_id')) {
            return $user->isAbleTo('dictionary-items-view')
                && $user->getCompanyId() == (request()->get('filter')['company_id'] ?? null);
        } elseif (! $canViewAllCompanies && ! request()->has('filter.company_id')) {
            return false;
        }

        return $user->isAbleTo('dictionary-items-view');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DictionaryItem  $dictionaryItem
     * @return mixed
     */
    public function view(User $user, DictionaryItem $dictionaryItem)
    {
        return $user->isAbleTo('dictionary-items-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $canViewAllCompanies = $user->isAbleTo('all-companies-view');

        if (! $canViewAllCompanies && request()->has('t_company_id')) {
            return $user->isAbleTo('dictionary-items-create')
                && $user->getCompanyId() == request()->get('t_company_id');
        } elseif (! $canViewAllCompanies && ! request()->has('t_company_id')) {
            return false;
        }

        return $user->isAbleTo('dictionary-items-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DictionaryItem  $dictionaryItem
     * @return mixed
     */
    public function update(User $user, DictionaryItem $dictionaryItem)
    {
        return $user->isAbleTo('dictionary-items-edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DictionaryItem  $dictionaryItem
     * @return mixed
     */
    public function delete(User $user, DictionaryItem $dictionaryItem)
    {
        return $user->isAbleTo('dictionary-items-remove');
    }
}
