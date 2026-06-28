<?php

declare(strict_types=1);

/**
 * @author Ohene Adjei
 */

namespace App\Policies;

use App\Models\SmsLog;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

/**
 * SMS Log rows store the literal message body, which for OTP sends includes
 * the verification code itself — only super_admin should be able to read
 * these, not every panel user, otherwise the OTP rate-limit/expiry
 * protections elsewhere in the app don't actually stop someone with admin
 * panel access from just reading the code straight out of the log.
 */
class SmsLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SmsLog');
    }

    public function view(AuthUser $authUser, SmsLog $smsLog): bool
    {
        return $authUser->can('View:SmsLog');
    }
}
