<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\SupportTickets;

use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Filament\Resources\SupportTickets\Tables\SupportTicketsTable;
use App\Models\SupportTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordRouteKeyName = 'uuid';

    public static function table(Table $table): Table
    {
        return SupportTicketsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportTickets::route('/'),
            'view' => ViewSupportTicket::route('/{record}'),
        ];
    }

    /**
     * I badge the nav item with the count of tickets still needing a reply,
     * mirroring the same "Open"/"In Progress" set SupportChatBubble uses to
     * decide whether a customer has an active conversation.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['Open', 'In Progress'])->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
