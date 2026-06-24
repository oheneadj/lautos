<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('causer.name')
                                ->label('Causer')
                                ->default('System'),
                            
                            TextEntry::make('description')
                                ->label('Event Type')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'created' => 'success',
                                    'updated' => 'warning',
                                    'deleted' => 'danger',
                                    default => 'info',
                                }),

                            TextEntry::make('subject_type')
                                ->label('Subject Type')
                                ->formatStateUsing(fn ($state) => class_basename($state)),

                            TextEntry::make('subject_id')
                                ->label('Subject ID'),

                            TextEntry::make('created_at')
                                ->label('Logged At')
                                ->dateTime(),
                        ]),
                    ]),

                Section::make('Changes')
                    ->schema([
                        Grid::make(2)->schema([
                            KeyValueEntry::make('properties.old')
                                ->label('Old Values')
                                ->visible(fn ($record) => isset($record->properties['old'])),
                                
                            KeyValueEntry::make('properties.attributes')
                                ->label('New Values')
                                ->visible(fn ($record) => isset($record->properties['attributes'])),
                        ]),
                    ])
                    ->visible(fn ($record) => isset($record->properties['old']) || isset($record->properties['attributes'])),
            ]);
    }
}
