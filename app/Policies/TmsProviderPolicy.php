<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TMSProvider;
use Illuminate\Auth\Access\HandlesAuthorization;

class TmsProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAbleTo('tms-providers-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TMSProvider $tmsProvider): bool
    {
        return $user->isAbleTo('tms-providers-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAbleTo('tms-providers-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TMSProvider $tmsProvider): bool
    {
        return $user->isAbleTo('tms-providers-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TMSProvider $tmsProvider): bool
    {
        return $user->isAbleTo('tms-providers-remove');
    }
}
