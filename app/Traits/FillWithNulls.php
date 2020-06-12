<?php

namespace App\Traits;

trait FillWithNulls
{
    public function fillWithNulls($only): self
    {
        return $this->fill(
            collect($this->fillable)
                ->filter(fn($column) => in_array($column, $only))
                ->mapWithKeys(fn($column) => [$column => null])
                ->toArray()
        );
    }
}
