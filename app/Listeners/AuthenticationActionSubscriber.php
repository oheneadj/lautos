<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;

class AuthenticationActionSubscriber
{
    public function handleUserLogin(Login $event): void
    {
        activity()
            ->causedBy($event->user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Logged in');
    }

    public function handleUserLogout(Logout $event): void
    {
        // $event->user can be null if they log out after being deleted or due to session expiry
        if ($event->user) {
            activity()
                ->causedBy($event->user)
                ->log('Logged out');
        }
    }

    public function handleFailedLogin(Failed $event): void
    {
        activity()
            ->causedBy($event->user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => $event->credentials['email'] ?? null,
            ])
            ->log('Failed login attempt');
    }

    public function handleTwoFactorEnabled(TwoFactorAuthenticationEnabled $event): void
    {
        activity()
            ->causedBy($event->user)
            ->log('Enabled Two-Factor Authentication');
    }

    public function handleTwoFactorDisabled(TwoFactorAuthenticationDisabled $event): void
    {
        activity()
            ->causedBy($event->user)
            ->log('Disabled Two-Factor Authentication');
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
            Failed::class => 'handleFailedLogin',
            TwoFactorAuthenticationEnabled::class => 'handleTwoFactorEnabled',
            TwoFactorAuthenticationDisabled::class => 'handleTwoFactorDisabled',
        ];
    }
}
