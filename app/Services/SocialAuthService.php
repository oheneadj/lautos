<?php

/**
 * Resolves a Socialite user into a local account — finds an existing
 * Google-linked customer, or creates a brand new one. Linking Google to an
 * already-existing, password-based account is a separate, explicit action
 * (linkGoogleAccount()), never automatic.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use RuntimeException;

class SocialAuthService
{
    /**
     * Finds the local account matching this Google identity, or creates a
     * new one. Google already verifies the email address, so I trust it
     * immediately rather than sending our own verification email. Phone
     * number isn't collected here — the customer adds it later from their
     * profile, same as any other gap in an existing account's KYC info.
     *
     * I deliberately do NOT auto-link to an existing, unlinked account by
     * email match alone — our own registration never verifies email
     * ownership, so an attacker could pre-register the victim's email with
     * a password, then silently inherit the victim's Google-verified
     * identity the moment the real owner signs in with Google. An existing
     * account has to be linked explicitly from the profile instead
     * (see linkGoogleAccount()).
     */
    public function findOrCreateFromGoogle(SocialiteUser $googleUser): User
    {
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            return $user;
        }

        $existing = User::where('email', $googleUser->getEmail())->first();

        if ($existing) {
            throw new RuntimeException('An account with this email already exists. Sign in with your password, then connect Google from your profile.');
        }

        $user = new User;
        $user->email = $googleUser->getEmail();
        $user->google_id = $googleUser->getId();
        $user->email_verified_at = now();
        $user->name = $googleUser->getName();
        // No password is ever set by the customer for a Google-only account —
        // I still need something in this required column, so I generate a
        // random one they'll never use unless they later set a real password.
        $user->password = Hash::make(Str::random(40));
        $user->has_password = false;
        $user->save();

        return $user;
    }

    /**
     * Links a Google identity to the currently authenticated customer's
     * account, from the Security settings page. Refuses to attach a Google
     * identity that's already linked to a different account — otherwise
     * one Google sign-in could end up usable on two separate local accounts.
     */
    public function linkGoogleAccount(User $user, SocialiteUser $googleUser): void
    {
        $linkedToSomeoneElse = User::where('google_id', $googleUser->getId())
            ->whereKeyNot($user->getKey())
            ->exists();

        if ($linkedToSomeoneElse) {
            throw new RuntimeException('This Google account is already connected to a different Livingston Autos account.');
        }

        $user->google_id = $googleUser->getId();
        $user->email_verified_at ??= now();
        $user->save();
    }
}
