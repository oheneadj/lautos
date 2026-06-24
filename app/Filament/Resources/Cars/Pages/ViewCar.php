<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Pages;

use App\Enums\CarBodyType;
use App\Enums\CarStatus;
use App\Filament\Resources\Cars\CarResource;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * A read-only view of one car — every field from the Edit form, plus the
 * orders placed on it, so an admin can see reservation/sale history
 * without having to cross-reference the Orders resource.
 */
class ViewCar extends ViewRecord
{
    protected static string $resource = CarResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->getRecord())
            ->components([
                Grid::make(1)
                    ->schema([
                        Section::make('Vehicle Details')
                            ->schema([
                                TextEntry::make('make.name')->label('Make'),
                                TextEntry::make('carModel.name')->label('Model'),
                                TextEntry::make('carTrim.name')->label('Trim')->placeholder('—'),
                                TextEntry::make('year'),
                                TextEntry::make('engine_capacity')->label('Engine Capacity'),
                                TextEntry::make('transmission'),
                                TextEntry::make('fuel_type')->label('Fuel Type'),
                                TextEntry::make('mileage')->suffix(' km'),
                                TextEntry::make('colour'),
                                TextEntry::make('country_of_origin')->label('Country of Origin'),
                                TextEntry::make('body_type')
                                    ->label('Body Type')
                                    ->formatStateUsing(fn (?CarBodyType $state): string => $state?->label() ?? '—'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (CarStatus $state): string => $state->colour()),
                            ])->columns(3),

                        Section::make('Pricing')
                            ->schema([
                                TextEntry::make('price_usd')
                                    ->label('Price (USD)')
                                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
                                TextEntry::make('price_ghs')
                                    ->label('Price (GHS equivalent)')
                                    ->formatStateUsing(fn ($state) => 'GH₵' . number_format($state, 2)),
                                TextEntry::make('shipping_cost_usd')
                                    ->label('Shipping Cost (USD)')
                                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
                                TextEntry::make('special_features')
                                    ->label('Special Features')
                                    ->badge()
                                    ->placeholder('—'),
                            ])->columns(2),
                    ]),

                Section::make('Photos')
                    ->schema([
                        ImageEntry::make('images.path')
                            ->label('')
                            ->disk('public')
                            ->height(120),
                    ]),

                Section::make('Orders on this car')
                    ->description('Every order ever placed for this car — useful context an Edit form does not surface.')
                    ->schema([
                        RepeatableEntry::make('orders')
                            ->label('')
                            ->schema([
                                TextEntry::make('user.name')->label('Customer'),
                                TextEntry::make('status')->badge()->color(fn ($state) => $state->colour()),
                                TextEntry::make('created_at')->label('Placed')->dateTime(),
                            ])
                            ->columns(3)
                            ->placeholder('No orders have been placed on this car yet.'),
                    ]),
            ])->columns(2);
    }
}
