<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\KycStatus;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

// I implement FilamentUser so canAccessPanel() is actually consulted — without it,
// Filament's Authenticate middleware ignores the method entirely and falls back to
// allowing access only when app.env is 'local', which silently 403s everywhere else.
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'google_id',
        'password',
        'phone',
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
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            // I encrypt KYC identifiers at rest — these never leave the DB unencrypted.
            'ghana_card_number'  => 'encrypted',
            'tin_number'         => 'encrypted',
            'kyc_status'         => KycStatus::class,
            'is_admin'           => 'boolean',
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
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->is_admin && $this->roles()->exists();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function savedCars(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
}
