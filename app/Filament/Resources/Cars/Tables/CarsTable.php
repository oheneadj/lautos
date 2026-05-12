<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Tables;

use App\Enums\CarStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')
                    ->sortable()
                    ->width('80px'),
                TextColumn::make('make.name')
                    ->label('Make')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('carModel.name')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('transmission')
                    ->toggleable(),
                TextColumn::make('fuel_type')
                    ->toggleable(),
                TextColumn::make('mileage')
                    ->numeric()
                    ->sortable()
                    ->suffix(' km'),
                TextColumn::make('price_usd_cents')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => '$' . number_format($state / 100, 2)),
                TextColumn::make('shipping_cost_usd_cents')
                    ->label('Shipping')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => '$' . number_format($state / 100, 2))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (CarStatus $state): string => $state->colour()),
                TextColumn::make('country_of_origin')
                    ->label('Origin')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-truck')
            ->emptyStateHeading('No cars listed yet')
            ->emptyStateDescription('Add your first car to start building the inventory.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Add first car')
                    ->icon('heroicon-m-plus'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CarStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->button(),
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
