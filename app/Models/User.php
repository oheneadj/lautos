<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\KycStatus;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable as BreezyTwoFactorAuthenticatable;
// I implement FilamentUser so canAccessPanel() is actually consulted — without it,
// Filament's Authenticate middleware ignores the method entirely and falls back to
// allowing access only when app.env is 'local', which silently 403s everywhere else.
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use BreezyTwoFactorAuthenticatable, HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'google_id',
        'password',
        'has_password',
        'phone',
        'phone_verified_at',
        'phone_verification_code',
        'phone_verification_code_expires_at',
        'address',
        'ghana_card_number',
        'tin_number',
        'ghana_card_path',
        'tin_path',
        'kyc_status',
        'kyc_notes',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'phone_verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
            'has_password' => 'boolean',
            // I encrypt KYC identifiers at rest — these never leave the DB unencrypted.
            'ghana_card_number' => 'encrypted',
            'tin_number' => 'encrypted',
            'kyc_status' => KycStatus::class,
            'is_admin' => 'boolean',
        ];
    }

    /**
     * I route by uuid everywhere — the integer id must never appear in an
     * admin URL or anywhere else (CLAUDE.md security rule).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        // I auto-generate uuid on creation so the caller never has to remember.
        static::creating(function (self $user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // I check for an assigned Spatie role rather than just is_admin, since that's what
    // Shield's policies and the super_admin gate actually key off of.
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin && $this->roles()->exists();
    }

    /**
     * I route SMS to the user's phone number for the GiantSMS channel.
     */
    public function routeNotificationForGiantsms(): ?string
    {
        return $this->phone;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function savedCars(): BelongsToMany
    {
        // I track timestamps on the pivot so the dashboard's "recently
        // saved" ordering actually means something instead of sorting on
        // permanently-null columns.
        return $this->belongsToMany(Car::class, 'car_user')->withTimestamps();
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
