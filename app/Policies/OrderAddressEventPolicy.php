<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrderAddressEvent;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderAddressEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAbleTo('order-address-events-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderAddressEvent $orderAddressEvent): bool
    {
        return $user->isAbleTo('order-address-events-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAbleTo('order-address-events-create') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderAddressEvent $orderAddressEvent): bool
    {
        return $user->isAbleTo('order-address-events-edit') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderAddressEvent $orderAddressEvent): bool
    {
        return $user->isAbleTo('order-address-events-remove') && ! request_is_from_nova();
    }
}
