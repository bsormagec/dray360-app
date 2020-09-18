<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EquipmentType;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipmentTypePolicy
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
        return $user->isAbleTo('equipment-types-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EquipmentType $equipmentType)
    {
        return $user->isAbleTo('equipment-types-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isAbleTo('equipment-types-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EquipmentType $equipmentType)
    {
        return $user->isAbleTo('equipment-types-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EquipmentType $equipmentType)
    {
        return $user->isAbleTo('equipment-types-remove');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EquipmentType $equipmentType)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EquipmentType $equipmentType)
    {
        return false;
    }
}
