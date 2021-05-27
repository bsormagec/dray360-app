<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OCRRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class OcrRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAbleTo('ocr-requests-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OCRRequest $ocrRequest): bool
    {
        return $user->isAbleTo('ocr-requests-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAbleTo('ocr-requests-create') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OCRRequest $ocrRequest): bool
    {
        if ($ocrRequest->isLockedForTheUser()) {
            return false;
        }

        return $user->isAbleTo('ocr-requests-edit') && ! request_is_from_nova();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OCRRequest $ocrRequest): bool
    {
        $hasDeletePermissions = $user->isAbleTo('ocr-requests-remove');

        if ($ocrRequest->isLockedForTheUser()) {
            return false;
        }

        if (! $user->isAbleTo('all-companies-view')) {
            $ocrRequestCompany = $ocrRequest->latestOcrRequestStatus->company_id ?? null;
            return $hasDeletePermissions && $user->getCompanyId() == $ocrRequestCompany;
        }

        return $hasDeletePermissions && ! request_is_from_nova();
    }

    /**
     * Determine if the user can send to the request orders to the tms.
     */
    public function sendToTms(User $user, OCRRequest $ocrRequest): bool
    {
        $hasPermissionsToSendToTms = $user->isAbleTo('tms-submit');

        if ($ocrRequest->isLockedForTheUser()) {
            return false;
        }

        if (! $user->isAbleTo('all-companies-view')) {
            $ocrRequestCompany = $ocrRequest->latestOcrRequestStatus->company_id ?? null;
            return $hasPermissionsToSendToTms && $user->getCompanyId() == $ocrRequestCompany;
        }

        return $hasPermissionsToSendToTms;
    }
}
