<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Customers\Tables;

use App\Enums\KycStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('kyc_status')
                    ->label('KYC Status')
                    ->badge()
                    ->color(fn (KycStatus $state): string => $state->colour()),
                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-identification')
            ->emptyStateHeading('No customers yet')
            ->emptyStateDescription('Customers will appear here once they register an account.')
            ->filters([
                SelectFilter::make('kyc_status')
                    ->options(KycStatus::class),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->button(),
            ]);
    }
}
