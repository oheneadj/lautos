<?php

/**
 * Resolves a Socialite user into a local account — links an existing
 * customer by email, or creates a brand new one.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthService
{
    /**
     * Finds the local account matching this Google identity, linking it by
     * email on first sign-in, or creates a new one. Google already verifies
     * the email address, so I trust it immediately rather than sending our
     * own verification email. Phone number isn't collected here — the
     * customer adds it later from their profile, same as any other gap in
     * an existing account's KYC info.
     */
    public function findOrCreateFromGoogle(SocialiteUser $googleUser): User
    {
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            return $user;
        }

        $user = User::firstOrNew(['email' => $googleUser->getEmail()]);
        $isNewAccount = ! $user->exists;

        $user->google_id = $googleUser->getId();
        $user->email_verified_at ??= now();

        if ($isNewAccount) {
            $user->name = $googleUser->getName();
            // No password is ever set by the customer for a Google-only account —
            // I still need something in this required column, so I generate a
            // random one they'll never use unless they later set a real password.
            $user->password = Hash::make(Str::random(40));
        }

        $user->save();

        return $user;
    }
}
