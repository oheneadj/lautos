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
                // I use aside() sections throughout this page — a settings screen
                // reads better as a stacked list of "label/description on the left,
                // fields on the right" rows than as one dense grid of inputs.
                Section::make('Branding')
                    ->description('Your business name and logo, shown across the site, dashboard, and emails.')
                    ->aside()
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Business Name')
                            ->placeholder('e.g. Livingston Autos'),

                        FileUpload::make('site_logo_path')
                            ->label('Logo')
                            ->image()
                            ->imagePreviewHeight('80')
                            ->disk('public')
                            ->directory('branding'),
                    ]),

                Section::make('Contact Details')
                    ->description('How customers reach you — shown on the Contact page and in emails.')
                    ->aside()
                    ->icon(Heroicon::OutlinedPhone)
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_email')->label('Contact Email')->email()->placeholder('e.g. info@livingstonautos.com'),
                        TextInput::make('contact_phone')->label('Contact Phone')->tel()->placeholder('e.g. +233 24 000 0000'),
                        TextInput::make('whatsapp_number')->label('WhatsApp Number')->tel()->placeholder('e.g. +233 55 000 0000'),
                        TextInput::make('contact_address')->label('Address')->placeholder('e.g. Accra, Ghana'),
                    ]),

                Section::make('Social Media')
                    ->description('Linked from the site footer. Leave blank to hide a platform.')
                    ->aside()
                    ->icon(Heroicon::OutlinedShare)
                    ->schema([
                        TextInput::make('facebook_url')->label('Facebook URL')->url()->placeholder('https://facebook.com/yourpage'),
                        TextInput::make('instagram_url')->label('Instagram URL')->url()->placeholder('https://instagram.com/yourhandle'),
                        TextInput::make('twitter_url')->label('Twitter / X URL')->url()->placeholder('https://x.com/yourhandle'),
                    ]),

                Section::make('About Us')
                    ->description('A short description of the business, shown on the public About page.')
                    ->aside()
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->schema([
                        Textarea::make('about_us')
                            ->label('About Us')
                            ->placeholder('A short description of the business shown on the About page.')
                            ->rows(3),
                    ]),

                Section::make('Exchange Rate')
                    ->description('Used to show GHS prices on every car listing. Last updated: ' . (Setting::get('exchange_rate_usd_to_ghs_updated_at') ?? 'never'))
                    ->aside()
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->schema([
                        TextInput::make('exchange_rate_usd_to_ghs')
                            ->label('USD to GHS Rate')
                            ->numeric()
                            ->step(0.01)
                            ->placeholder('e.g. 15.50')
                            ->required()
                            ->disabled(fn () => Gate::denies('update_exchange_rate'))
                            ->helperText(fn () => Gate::denies('update_exchange_rate') ? 'You don\'t have permission to change this.' : null),
                    ]),

                Section::make('Payment Details')
                    ->description('Shown to customers when they need to pay for an order. Only a Super Admin can change these.')
                    ->aside()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columns(2)
                    ->schema([
                        TextInput::make('bank_name')->placeholder('e.g. GCB Bank')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('bank_account_name')->label('Account Name')->placeholder('e.g. Livingston Autos Ltd')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('bank_account_number')->label('Account Number')->placeholder('e.g. 1234567890')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('momo_number')->label('MoMo Number')->placeholder('e.g. +233 55 000 0000')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('momo_name')->label('MoMo Name')->placeholder('e.g. Livingston Autos')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                    ]),

                Section::make('Demurrage Warning')
                    ->description('Shown on car listings to warn customers about port storage penalties.')
                    ->aside()
                    ->icon(Heroicon::OutlinedExclamationTriangle)
                    ->schema([
                        Textarea::make('demurrage_warning')
                            ->label('Warning Message')
                            ->placeholder('e.g. Clearing fees and demurrage charges are paid separately at the port...')
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
