<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrdersPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAbleTo('orders-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        $hasViewPermission = $user->isAbleTo('orders-view');

        if (! $user->isSuperadmin()) {
            return $hasViewPermission && $user->belongsToSameCompanyAs($order);
        }

        return $hasViewPermission;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function create(User $user): bool
    {
        return $user->isAbleTo('orders-create') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        $hasUpdatePermissions = $user->isAbleTo('orders-edit');

        if (! $user->isSuperadmin()) {
            return $hasUpdatePermissions && $user->belongsToSameCompanyAs($order);
        }

        return $hasUpdatePermissions;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order)
    {
        $hasDeletePermissions = $user->isAbleTo('orders-remove');

        if (! $user->isSuperadmin()) {
            return $hasDeletePermissions && $user->belongsToSameCompanyAs($order);
        }

        return $hasDeletePermissions && ! request_is_from_nova();
    }

    /**
     * Determine if the user can send to an order to the tms.
     */
    public function sendToTms(User $user, Order $order): bool
    {
        $hasPermissionsToSendToTms = $user->isAbleTo('tms-submit');

        if (! $user->isSuperadmin()) {
            return $hasPermissionsToSendToTms && $user->belongsToSameCompanyAs($order);
        }

        return $hasPermissionsToSendToTms;
    }

    /**
     * Determine if the user can send the order to the client.
     */
    public function review(User $user, Order $order): bool
    {
        $hasPermissionsToSendToClient = $user->isAbleTo('admin-review-edit');

        if (! $user->isSuperadmin()) {
            return $hasPermissionsToSendToClient && $user->belongsToSameCompanyAs($order);
        }

        return $hasPermissionsToSendToClient;
    }

    /**
     * Determine if the user can replicate an order.
     */
    public function replicate(User $user, Order $order): bool
    {
        $hasPermissionToReplicate = $user->isAbleTo('admin-review-edit');

        if (! $user->isSuperadmin()) {
            return $hasPermissionToReplicate && $user->belongsToSameCompanyAs($order);
        }

        return $hasPermissionToReplicate;
    }
}
