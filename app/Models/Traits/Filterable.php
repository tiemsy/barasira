<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, $filters)
    {
        $class = $this->filtersClass ?? null;

        if (!$class || !class_exists($class)) {
            return $query;
        }

        return (new $class($query, $filters))->apply();
    }
}
