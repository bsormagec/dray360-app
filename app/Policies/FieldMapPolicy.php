<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FieldMap;
use Illuminate\Auth\Access\HandlesAuthorization;

class FieldMapPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isAbleTo('field-maps-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FieldMap $fieldMap)
    {
        return $user->isAbleTo('field-maps-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isAbleTo('field-maps-create') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FieldMap $fieldMap)
    {
        return $user->isAbleTo('field-maps-edit') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FieldMap $fieldMap)
    {
        return $user->isAbleTo('field-maps-remove') && ! request_is_from_nova();
    }
}
