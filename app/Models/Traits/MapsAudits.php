<?php

namespace App\Models\Traits;

use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Collection;

trait MapsAudits
{
    /**
     * Return a list of attributes with their changes.
     */
    public function getAttributesChanges(): Collection
    {
        return $this->audits
            ->flatMap(fn ($audit) => $this->getModifiedAudit($audit->setRelation('auditable', $this)))
            ->groupBy('attribute')
            ->map(function ($changes) {
                return collect($changes)->sortBy(function ($change) {
                    return $change['updated_at']->getTimestamp();
                });
            });
    }

    public function getModifiedAudit(Audit $audit): Collection
    {
        return collect($audit->getModified())
            ->map(function ($modified, $attribute) use ($audit) {
                return $modified + [
                    'old' => $audit->old,
                    'user' => $audit->user->name ?? null,
                    'attribute' => $attribute,
                    'updated_at' => $audit->created_at,
                ];
            })
            ->values();
    }
}
