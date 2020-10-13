<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OCRRequestStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class OcrRequestStatusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isAbleTo('ocr-request-statuses-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OCRRequestStatus $oCRRequestStatus)
    {
        return $user->isAbleTo('ocr-request-statuses-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isAbleTo('ocr-request-statuses-create') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OCRRequestStatus $oCRRequestStatus)
    {
        return $user->isAbleTo('ocr-request-statuses-edit') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OCRRequestStatus $oCRRequestStatus)
    {
        return $user->isAbleTo('ocr-request-statuses-remove') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OCRRequestStatus $oCRRequestStatus)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OCRRequestStatus $oCRRequestStatus)
    {
        return false;
    }
}
