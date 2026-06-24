<?php

namespace App\Providers;

use App\Channels\GiantSmsChannel;
use App\Models\Setting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Listeners\AuthenticationActionSubscriber;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // I redirect to the KYC step after registration so customers
        // can provide their Ghana Card / TIN before hitting the dashboard.
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('register.kyc');
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->syncBusinessNameFromSettings();

        // I register 'giantsms' as a notification channel here — without this,
        // any notification returning it from via() would fail to resolve.
        Notification::extend('giantsms', fn () => new GiantSmsChannel());

        Event::subscribe(AuthenticationActionSubscriber::class);
    }

    /**
     * I let the admin-editable site_name setting override config('app.name')
     * everywhere it's already used (mail templates, page titles) instead of
     * inventing a second "business name" helper just for the DB value.
     */
    protected function syncBusinessNameFromSettings(): void
    {
        // I guard with hasTable() because this runs on every boot, including
        // a fresh install's first `migrate` before the settings table exists.
        if (! Schema::hasTable('settings')) {
            return;
        }

        config(['app.name' => Setting::get('site_name', config('app.name'))]);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
