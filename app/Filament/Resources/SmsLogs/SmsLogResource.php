<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\SmsLogs;

use App\Filament\Resources\SmsLogs\Pages\ListSmsLogs;
use App\Filament\Resources\SmsLogs\Pages\ViewSmsLog;
use App\Filament\Resources\SmsLogs\Schemas\SmsLogInfolist;
use App\Filament\Resources\SmsLogs\Tables\SmsLogsTable;
use App\Models\SmsLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SmsLogResource extends Resource
{
    protected static ?string $model = SmsLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'SMS Logs';

    protected static ?int $navigationSort = 1;

    // These rows only ever come from GiantSmsService — never created or edited by hand.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return SmsLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmsLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsLogs::route('/'),
            'view' => ViewSmsLog::route('/{record}'),
        ];
    }
}
