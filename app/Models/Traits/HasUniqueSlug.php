<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUniqueSlug
{
    public static function bootHasUniqueSlug(): void
    {
        static::creating(function (Model $model): void {
            if (blank($model->slug)) {
                $model->slug = $model->uniqueSlug($model->slugSource());
            }
        });
    }

    abstract protected function slugSource(): string;

    protected function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: Str::singular($this->getTable());
        if (ctype_digit($base)) {
            $base = Str::singular($this->getTable()).'-'.$base;
        }
        $slug = $base;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
