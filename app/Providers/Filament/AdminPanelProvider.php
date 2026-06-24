<?php

/**
 * Registers and configures the Filament admin panel at /admin.
 *
 * @author Ohene Adjei
 */

namespace App\Providers\Filament;

use App\Models\Setting;
use App\Livewire\Admin\ProfilePhoneInfo;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            // I resolve these as closures (not plain strings) so they're evaluated
            // per-request and always reflect the current Setting, not whatever was
            // true when the panel was registered.
            ->brandName(fn () => config('app.name'))
            ->brandLogo(fn () => Setting::get('site_logo_path')
                ? \Illuminate\Support\Facades\Storage::url(Setting::get('site_logo_path'))
                : null)
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            // I set this explicitly so the sidebar follows the actual business
            // workflow (catalogue → orders → customers → content) instead of
            // Filament's default alphabetical group order.
            ->navigationGroups([
                'Inventory',
                'Orders',
                'Customers',
                'Content',
                'Administration',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                // I replace Filament's built-in ->profile() with Breezy's richer
                // My Profile page so admins get password rules, 2FA, and our
                // custom phone-verification component all in one place.
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                    )
                    ->myProfileComponents([
                        ProfilePhoneInfo::class,
                    ])
                    ->enableTwoFactorAuthentication(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
