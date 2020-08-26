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
     */
    public function viewAny(User $user)
    {
        return $user->isAbleTo('rules-editor-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OCRRule $rule): bool
    {
        return $user->isAbleTo('rules-editor-edit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isAbleTo('rules-editor-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OCRRule $ocrRule)
    {
        return $user->isAbleTo('rules-editor-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OCRRule $model): bool
    {
        return $user->isAbleTo('rules-editor-edit');
    }

    /**
     * Determine whether the user can assign the model.
     */
    public function assign(User $user)
    {
        return $user->isAbleTo('rules-editor-assign');
    }
}
