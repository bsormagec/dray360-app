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
     * Determine if the user can download an order pdf.
     */
    public function downloadPdf(User $user, Order $order): bool
    {
        if (! $user->isSuperadmin()) {
            return $user->belongsToSameCompanyAs($order);
        }

        return true;
    }
}
