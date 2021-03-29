<?php

namespace App\Models\Traits;

use App\Models\ObjectLock;

trait HasLocks
{
    public function locks()
    {
        return $this->hasMany(ObjectLock::class, 'object_id', $this->getObjectLocalKey())
            ->where('object_type', $this->getObjectLockType());
    }

    public function getActiveLock($relationship = 'locks'): ?ObjectLock
    {
        return $this->getRelationValue($relationship)
            ->filter(fn ($lock) => $lock->isActive())
            ->sortBy(fn ($item) => $item->id)
            ->first();
    }

    public function isLockedForTheUser($relationship = 'locks'): bool
    {
        $lock = $this->getActiveLock($relationship);

        return $lock && $lock->user_id != auth()->id();
    }

    protected function getObjectLockType(): string
    {
        return property_exists($this, 'objectLockType')
            ? $this->objectLockType
            : self::class;
    }

    protected function getObjectLocalKey(): string
    {
        return property_exists($this, 'objectLockLocalKey')
            ? $this->objectLockLocalKey
            : 'id';
    }
}
