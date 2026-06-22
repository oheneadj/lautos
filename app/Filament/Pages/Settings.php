<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\SettingsService;
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
                        TextInput::make('account_name')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
                        TextInput::make('account_number')->disabled(fn () => ! Auth::user()?->hasRole('super_admin')),
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
