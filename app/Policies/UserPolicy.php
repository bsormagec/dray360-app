<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAbleTo('users-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model)
    {
        return $user->isAbleTo('users-view') && $user->belongsToSameCompanyAs($model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAbleTo('users-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAbleTo('users-edit') && $user->belongsToSameCompanyAs($model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAbleTo('users-remove') && $user->belongsToSameCompanyAs($model);
    }

    /**
     * Determine if the current user can bulk update users.
     */
    public function bulkUpdate(User $user, array $ids): bool
    {
        return $user->isAbleTo('users-edit')
            && User::forCurrentCompany()
                ->whereIn('id', $ids)
                ->count() == count($ids);
    }

    /**
     * Determine if the current user can bulk delete users.
     */
    public function bulkDelete(User $user, array $ids): bool
    {
        return $user->isAbleTo('users-remove')
            && User::forCurrentCompany()
                ->whereIn('id', $ids)
                ->count() == count($ids);
    }
}
