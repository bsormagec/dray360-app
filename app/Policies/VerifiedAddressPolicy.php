<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VerifiedAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class VerifiedAddressPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAbleTo('verified-addresses-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VerifiedAddress $verifiedAddress): bool
    {
        return $user->isAbleTo('verified-addresses-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAbleTo('verified-addresses-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VerifiedAddress $verifiedAddress): bool
    {
        return $user->isAbleTo('verified-addresses-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VerifiedAddress $verifiedAddress): bool
    {
        return $user->isAbleTo('verified-addresses-remove');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VerifiedAddress $verifiedAddress): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VerifiedAddress $verifiedAddress): bool
    {
        return false;
    }
}
