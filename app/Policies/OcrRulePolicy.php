<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OCRRule;
use Illuminate\Auth\Access\HandlesAuthorization;

class OcrRulePolicy
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
        return $user->isAbleTo('rules-editor-view');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OCRRule  $ocrRule
     * @return mixed
     */
    public function assign(User $user)
    {
        return $user->isAbleTo('rules-editor-assign');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAbleTo('rules-editor-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OCRRule  $ocrRule
     * @return mixed
     */
    public function update(User $user, OCRRule $ocrRule)
    {
        return $user->isAbleTo('rules-editor-edit');
    }
}
