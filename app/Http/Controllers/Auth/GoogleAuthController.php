<?php

/**
 * Handles "Continue with Google" — redirects to Google and logs the
 * customer in (or registers them) on the way back. Also handles connecting
 * Google to an already-authenticated account from the Security settings page.
 *
 * @author Ohene Adjei
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Same Google redirect, but flagged as a "connect to my existing
     * account" request rather than a sign-in — the callback below checks
     * this flag to decide which behaviour to run.
     */
    public function redirectToLink(): RedirectResponse
    {
        session(['google_link_intent' => true]);

        return Socialite::driver('google')->redirect();
    }

    public function callback(SocialAuthService $socialAuthService): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            // I land on login rather than letting Google's error bubble up raw —
            // this happens whenever someone cancels the consent screen or the
            // OAuth state has expired, not just on a real failure.
            Log::warning('Google OAuth callback failed.', ['message' => $e->getMessage()]);

            return redirect()->route('login')->with('status', __('Google sign-in was cancelled or failed. Please try again.'));
        }

        if (session()->pull('google_link_intent') && Auth::check()) {
            try {
                $socialAuthService->linkGoogleAccount(Auth::user(), $googleUser);
            } catch (RuntimeException $e) {
                return redirect()->route('security.edit')->with('status', $e->getMessage());
            }

            return redirect()->route('security.edit')->with('status', __('Google account connected.'));
        }

        try {
            $user = $socialAuthService->findOrCreateFromGoogle($googleUser);
        } catch (RuntimeException $e) {
            return redirect()->route('login')->with('status', $e->getMessage());
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard.index'));
    }
}
