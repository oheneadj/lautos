<?php

/**
 * Handles "Continue with Google" — redirects to Google and logs the
 * customer in (or registers them) on the way back.
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
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
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

        $user = $socialAuthService->findOrCreateFromGoogle($googleUser);

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard.index'));
    }
}
