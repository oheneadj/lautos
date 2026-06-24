<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\SettingsService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * A single settings screen for the exchange rate, payment details, and the
 * demurrage warning message — no developer needed to change these.
 */
class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(app(SettingsService::class)->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Business Info')
                    ->description('Shown across the public site, customer dashboard, and emails.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Business Name')
                            ->columnSpanFull(),

                        FileUpload::make('site_logo_path')
                            ->label('Logo')
                            ->image()
                            ->imagePreviewHeight('80')
                            ->disk('public')
                            ->directory('branding')
                            ->columnSpanFull(),

                        TextInput::make('contact_email')->label('Contact Email')->email(),
                        TextInput::make('contact_phone')->label('Contact Phone')->tel(),
                        TextInput::make('whatsapp_number')->label('WhatsApp Number')->tel(),
                        TextInput::make('contact_address')->label('Address'),

                        TextInput::make('facebook_url')->label('Facebook URL')->url(),
                        TextInput::make('instagram_url')->label('Instagram URL')->url(),
                        TextInput::make('twitter_url')->label('Twitter / X URL')->url(),

                        Textarea::make('about_us')
                            ->label('About Us')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Exchange Rate')
                    ->description('Last updated: ' . (Setting::get('exchange_rate_usd_to_ghs_updated_at') ?? 'never'))
                    ->schema([
                        TextInput::make('exchange_rate_usd_to_ghs')
                            ->label('USD to GHS Rate')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->disabled(fn () => Gate::denies('update_exchange_rate')),
                    ]),

                Section::make('Payment Details')
                    ->description('Only Super Admin can change these.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('bank_name')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('bank_account_name')->label('Account Name')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('bank_account_number')->label('Account Number')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('momo_number')->label('MoMo Number')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('momo_name')->label('MoMo Name')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                    ]),

                Section::make('Demurrage Warning')
                    ->schema([
                        Textarea::make('demurrage_warning')
                            ->label('Warning Message')
                            ->rows(3),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $service = app(SettingsService::class);

        foreach ($data as $key => $value) {
            $service->update($key, $value);
        }

        if (array_key_exists('exchange_rate_usd_to_ghs', $data)) {
            Setting::set('exchange_rate_usd_to_ghs_updated_at', now()->toDateTimeString());
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
