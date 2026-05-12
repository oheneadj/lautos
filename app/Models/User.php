<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\KycStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'email',
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

    // I implement this so Filament Shield can check admin access.
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->is_admin;
    }
}
