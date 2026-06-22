<?php

/**
 * Reads and writes system settings, with caching handled by the Setting
 * model itself. Every write is logged to the activity log with the old
 * and new value, since these affect prices shown across the whole site.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class SettingsService
{
    /**
     * @var array<int, string>
     */
    public const KEYS = [
        'exchange_rate_usd_to_ghs',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'momo_number',
        'momo_name',
        'demurrage_warning',
    ];

    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => Setting::get($key)])->all();
    }

    public function update(string $key, mixed $value): void
    {
        $oldValue = Setting::get($key);

        if ((string) $oldValue === (string) $value) {
            return;
        }

        Setting::set($key, $value);

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['key' => $key, 'old' => $oldValue, 'new' => $value])
            ->log("Updated setting: {$key}");
    }
}
