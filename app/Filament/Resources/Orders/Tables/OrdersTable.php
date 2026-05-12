<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
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

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('Order #')
                    ->searchable()
                    ->copyable()
                    ->limit(8)
                    ->tooltip(fn ($record) => $record->uuid),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('car.make')
                    ->label('Car')
                    ->formatStateUsing(
                        fn ($record) => "{$record->car?->year} {$record->car?->make->name} {$record->car?->model}"
                    )
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => $state->colour()),
                TextColumn::make('price_usd_cents')
                    ->label('Total')
                    ->formatStateUsing(fn ($record) => '$' . number_format($record->total_usd_cents / 100, 2))
                    ->sortable(),
                TextColumn::make('estimated_arrival_date')
                    ->label('Est. Arrival')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Placed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateHeading('No orders yet')
            ->emptyStateDescription('Orders will appear here once customers place them.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Create first order')
                    ->icon('heroicon-m-plus'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(OrderStatus::class),
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
