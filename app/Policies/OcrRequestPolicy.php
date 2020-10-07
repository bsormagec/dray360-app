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
        return $user->isAbleTo('ocr-requests-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OCRRequest $ocrRequest): bool
    {
        return $user->isAbleTo('ocr-requests-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OCRRequest $ocrRequest): bool
    {
        return $user->isAbleTo('ocr-requests-remove');
    }
}
