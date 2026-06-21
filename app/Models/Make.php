<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Make extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon_path',
    ];

    /**
     * I route by slug everywhere Laravel generates a link from a model instance, so the
     * integer id never appears in catalogue filter links.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (self $make) {
            if (empty($make->slug)) {
                $make->slug = static::uniqueSlug(Str::slug($make->name));
            }
        });
    }

    // I append an incrementing suffix (-2, -3 …) until the slug is unique, same pattern as Car/BlogPost.
    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug   = $base;
        $suffix = 2;

        while (
            static::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class)->orderBy('name');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
