<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Deactivatable
{
    public function setDeactivatedAtAttribute($value)
    {
        if (is_bool($value)) {
            $this->attributes['deactivated_at'] = $value ? now() : null;
            return;
        }

        $this->attributes['deactivated_at'] = $value;
    }

    public function deactivate(bool $save = true): self
    {
        $this->deactivated_at = now();

        if ($save) {
            $this->save();
        }

        return $this;
    }

    public function activate(bool $save = true): self
    {
        $this->deactivated_at = null;

        if ($save) {
            $this->save();
        }

        return $this;
    }

    public function scopeActive(Builder $builder, $active = true)
    {
        if ($active) {
            return $builder->whereNull($this->qualifyColumn('deactivated_at'));
        }

        return $builder->whereNotNull($this->qualifyColumn('deactivated_at'));
    }

    public function isActive(): bool
    {
        return ! $this->deactivated_at;
    }
}
