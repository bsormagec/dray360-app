<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectLock extends Model
{
    use SoftDeletes;

    const REQUEST_OBJECT = 'request',
        ORDER_OBJECT = 'order',
        CLAIM_LOCK_TYPE = 'claim-lock',
        SELECT_REQUEST_TYPE = 'select-request',
        OPEN_ORDER_TYPE = 'open-order';
    const REFRESH_INTERVAL_SECONDS = 30;

    public $table = 't_object_locks';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'object_type',
        'object_id',
        'lock_type',
        'user_id',
        'created_at'
    ];

    public static function rules()
    {
        return [
            'object_type' => [
                'required',
                Rule::in([static::ORDER_OBJECT, static::REQUEST_OBJECT])
            ],
            'object_id' => 'required|alpha_dash',
            'lock_type' => [
                'required',
                Rule::in([
                    static::CLAIM_LOCK_TYPE,
                    static::OPEN_ORDER_TYPE,
                    static::SELECT_REQUEST_TYPE,
                ]),
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function findFor(string $objectType, $objectId, $userId = null): ?self
    {
        return static::query()
            ->where([
                'object_type' => $objectType,
                'object_id' => $objectId,
            ])
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->orderByDesc('id')
            ->first();
    }

    public static function existsActiveLock(string $objectType, $objectId, $userId = null)
    {
        $lock = static::findFor($objectType, $objectId, $userId);

        if (! $lock) {
            return false;
        }

        if (! $lock->isActive()) {
            $lock->delete();
            return false;
        }

        return true;
    }

    public function isActive(): bool
    {
        if ($this->updated_at) {
            return $this->updated_at->diffInRealSeconds(now()) <= static::REFRESH_INTERVAL_SECONDS;
        }

        return $this->created_at->diffInRealSeconds(now()) <= static::REFRESH_INTERVAL_SECONDS;
    }
}
