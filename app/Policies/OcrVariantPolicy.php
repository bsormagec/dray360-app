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
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAbleTo('ocr-variants-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAbleTo('ocr-variants-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OCRVariant  $oCRVariant
     * @return mixed
     */
    public function update(User $user, OCRVariant $oCRVariant)
    {
        return $user->isAbleTo('ocr-variants-edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OCRVariant  $oCRVariant
     * @return mixed
     */
    public function delete(User $user, OCRVariant $oCRVariant)
    {
        return $user->isAbleTo('ocr-variants-remove');
    }
}
