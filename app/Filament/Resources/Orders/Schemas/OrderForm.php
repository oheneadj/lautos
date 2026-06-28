<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Car;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->description('Who placed this order, on which car, and its current status.')
                    ->icon(Heroicon::OutlinedShoppingCart)
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a customer')
                            ->required(),
                        Select::make('car_id')
                            ->relationship('car', 'year')
                            ->getOptionLabelFromRecordUsing(
                                fn (Car $record) => "{$record->year} {$record->make->name} {$record->carModel->name}"
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a car')
                            ->required(),
                        Select::make('status')
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::PendingPayment)
                            ->placeholder('Select a status')
                            ->required(),
                    ]),

                Section::make('Pricing Snapshot')
                    ->description('Captured at order time — editing here does not affect the car listing price.')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->columns(2)
                    ->schema([
                        TextInput::make('price_usd_cents')
                            ->label('Price (USD)')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->step(0.01)
                            ->afterStateHydrated(fn ($state, $set) => $set('price_usd_cents', $state / 100))
                            ->dehydrateStateUsing(fn ($state) => (int) round($state * 100)),
                        TextInput::make('shipping_cost_usd_cents')
                            ->label('Shipping Cost (USD)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->step(0.01)
                            ->afterStateHydrated(fn ($state, $set) => $set('shipping_cost_usd_cents', $state / 100))
                            ->dehydrateStateUsing(fn ($state) => (int) round($state * 100)),
                    ]),

                Section::make('Logistics')
                    ->description('Shipping and delivery details shown on the customer\'s order tracking page.')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->columns(2)
                    ->schema([
                        DatePicker::make('estimated_arrival_date')
                            ->label('Est. Arrival Date')
                            ->placeholder('Select a date')
                            ->minDate(now()),
                        TextInput::make('tracking_number')
                            ->placeholder('e.g. MAEU123456789')
                            ->maxLength(100),
                        TextInput::make('vessel_name')
                            ->placeholder('e.g. MSC Olympia')
                            ->maxLength(100),
                        DateTimePicker::make('delivered_at')
                            ->label('Delivered At')
                            ->placeholder('Select date and time'),
                    ]),
            ]);
    }
}
