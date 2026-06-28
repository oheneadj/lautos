<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use App\Services\SessionService;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => $input['password'],
            'has_password' => true,
        ])->save();

        // I have nothing to exempt here — this resets a password the user
        // forgot, so there's no "current session" of theirs to preserve.
        // If an attacker has a session open, this is exactly the moment
        // we want it gone.
        app(SessionService::class)->deleteOtherSessions($user, exceptSessionId: null);
    }
}
