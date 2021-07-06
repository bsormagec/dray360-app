<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $commentable_id
 * @property int $user_id
 * @property string $commentable_type
 * @property string $comment
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class FeedbackComment extends Model
{
    const CLASSES_MAP = [
        ObjectLock::ORDER_OBJECT => Order::class,
        ObjectLock::REQUEST_OBJECT => OCRRequest::class,
    ];

    public $table = 't_feedback_comments';

    public $fillable = [
        'commentable_id',
        'user_id',
        'commentable_type',
        'comment',
    ];

    public function commentable()
    {
        return $this->morphTo('commentable');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
