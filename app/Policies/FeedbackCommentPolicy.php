<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FeedbackComment;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedbackCommentPolicy
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
        return $user->isAbleTo('feedbacks-view');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FeedbackComment  $feedbackComment
     * @return mixed
     */
    public function view(User $user, FeedbackComment $feedbackComment)
    {
        return $user->isAbleTo('feedbacks-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAbleTo('feedbacks-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FeedbackComment  $feedbackComment
     * @return mixed
     */
    public function update(User $user, FeedbackComment $feedbackComment)
    {
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FeedbackComment  $feedbackComment
     * @return mixed
     */
    public function delete(User $user, FeedbackComment $feedbackComment)
    {
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FeedbackComment  $feedbackComment
     * @return mixed
     */
    public function restore(User $user, FeedbackComment $feedbackComment)
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FeedbackComment  $feedbackComment
     * @return mixed
     */
    public function forceDelete(User $user, FeedbackComment $feedbackComment)
    {
    }
}
