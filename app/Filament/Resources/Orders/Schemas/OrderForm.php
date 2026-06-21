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

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('car_id')
                            ->relationship('car', 'year')
                            ->getOptionLabelFromRecordUsing(
                                fn (Car $record) => "{$record->year} {$record->make->name} {$record->carModel->name}"
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::PendingPayment)
                            ->required(),
                    ]),

                Section::make('Pricing Snapshot')
                    ->columns(2)
                    ->description('Captured at order time — editing here does not affect the car listing price.')
                    ->schema([
                        TextInput::make('price_usd_cents')
                            ->label('Price (USD)')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->afterStateHydrated(fn ($state, $set) => $set('price_usd_cents', $state / 100))
                            ->dehydrateStateUsing(fn ($state) => (int) round($state * 100)),
                        TextInput::make('shipping_cost_usd_cents')
                            ->label('Shipping Cost (USD)')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->afterStateHydrated(fn ($state, $set) => $set('shipping_cost_usd_cents', $state / 100))
                            ->dehydrateStateUsing(fn ($state) => (int) round($state * 100)),
                    ]),

                Section::make('Logistics')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('estimated_arrival_date')
                            ->label('Est. Arrival Date'),
                        TextInput::make('tracking_number')
                            ->maxLength(100),
                        TextInput::make('vessel_name')
                            ->maxLength(100),
                        DateTimePicker::make('delivered_at')
                            ->label('Delivered At'),
                    ]),
            ]);
    }
}
