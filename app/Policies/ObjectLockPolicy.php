<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ObjectLock;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObjectLockPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAbleTo('object-locks-create');
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function update(User $user, ObjectLock $lock): bool
    {
        return $user->isAbleTo('object-locks-edit');
    }
}
