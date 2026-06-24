<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\SupportTickets\Tables;

use App\Models\SupportTicket;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Open' => 'info',
                        'In Progress' => 'warning',
                        'Closed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Opened')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->emptyStateIcon('heroicon-o-lifebuoy')
            ->emptyStateHeading('No support tickets yet')
            ->emptyStateDescription('Customer tickets will show up here as soon as they open one.')
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(SupportTicket::STATUSES, SupportTicket::STATUSES)),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->button(),
            ]);
    }
}
