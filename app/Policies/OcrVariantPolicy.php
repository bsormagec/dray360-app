<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OCRVariant;
use Illuminate\Auth\Access\HandlesAuthorization;

class OcrVariantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isAbleTo('ocr-variants-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OCRVariant $oCRVariant): bool
    {
        return $user->isAbleTo('ocr-variants-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isAbleTo('ocr-variants-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OCRVariant $oCRVariant)
    {
        return $user->isAbleTo('ocr-variants-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OCRVariant $oCRVariant)
    {
        return $user->isAbleTo('ocr-variants-remove');
    }
}
