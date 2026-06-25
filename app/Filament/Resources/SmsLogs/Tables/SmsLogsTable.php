<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\SmsLogs\Tables;

use App\Enums\SmsLogStatus;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SmsLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('message')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('context')
                    ->label('Triggered by')
                    ->placeholder('—')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (SmsLogStatus $state) => $state->colour())
                    ->formatStateUsing(fn (SmsLogStatus $state) => $state->label()),
                TextColumn::make('http_status')
                    ->label('HTTP')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Sent at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->emptyStateHeading('No SMS sent yet')
            ->emptyStateDescription('Every OTP code and SMS notification sent through GiantSMS will show up here.')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        SmsLogStatus::Sent->value => SmsLogStatus::Sent->label(),
                        SmsLogStatus::Failed->value => SmsLogStatus::Failed->label(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
