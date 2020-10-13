<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Address;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isAbleTo('addresses-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Address $address)
    {
        return $user->isAbleTo('addresses-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isAbleTo('addresses-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Address $address)
    {
        return $user->isAbleTo('addresses-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address)
    {
        return $user->isAbleTo('addresses-remove') && false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Address $address)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Address $address)
    {
        return false;
    }
}
