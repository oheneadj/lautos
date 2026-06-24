<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')
                    ->label('User')
                    ->description(fn (Model $record) => $record->causer?->email)
                    ->url(fn (Model $record) => $record->causer_type === \App\Models\User::class && $record->causer_id 
                        ? route('filament.admin.resources.users.view', $record->causer_id) 
                        : null)
                    ->searchable()
                    ->sortable()
                    ->default('System'),

                TextColumn::make('description')
                    ->label('Event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'Logged in', 'Enabled Two-Factor Authentication' => 'info',
                        'Failed login attempt', 'Disabled Two-Factor Authentication' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(function (string $state, Model $record) {
                        $modelName = class_basename($state);
                        return $record->subject_id ? "{$modelName} #{$record->subject_id}" : $modelName;
                    })
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions allowed
            ]);
    }
}
