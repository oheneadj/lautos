<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\SmsLogs\Schemas;

use App\Enums\SmsLogStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SmsLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message')
                    ->columns(2)
                    ->components([
                        TextEntry::make('phone'),
                        TextEntry::make('context')
                            ->label('Triggered by')
                            ->placeholder('—'),
                        TextEntry::make('message')
                            ->columnSpanFull(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (SmsLogStatus $state) => $state->colour())
                            ->formatStateUsing(fn (SmsLogStatus $state) => $state->label()),
                        TextEntry::make('created_at')
                            ->label('Sent at')
                            ->dateTime(),
                    ]),

                Section::make('Gateway response')
                    ->columns(1)
                    ->components([
                        TextEntry::make('http_status')
                            ->label('HTTP status')
                            ->placeholder('—'),
                        TextEntry::make('error_message')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('response_body')
                            ->label('Raw response')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
