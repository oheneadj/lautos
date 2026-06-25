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

    // I memoize per-request on top of the 1-hour Cache::remember() below —
    // with the database cache driver, even a cache "hit" is its own SQL query,
    // and a single page (e.g. a 12-car catalogue grid) can call Setting::get()
    // for the same key a dozen times, so this avoids a dozen identical queries.
    private static array $requestCache = [];

    // I cache settings for 1 hour to avoid a DB hit on every page load.
    public static function get(string $key, mixed $default = null): mixed
    {
        if (\array_key_exists($key, static::$requestCache)) {
            return static::$requestCache[$key];
        }

        return static::$requestCache[$key] = Cache::remember("setting.{$key}", 3600,
            fn () => static::where('key', $key)->value('value') ?? $default
        );
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
        unset(static::$requestCache[$key]);
    }
}
