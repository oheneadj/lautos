<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    // ── Static helpers ───────────────────────────────────────────────────────

    // I cache settings for 1 hour to avoid a DB hit on every page load.
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600,
            fn () => static::where('key', $key)->value('value') ?? $default
        );
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }
}
