<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListSupportTickets extends ListRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action — tickets only ever originate from a customer.
        ];
    }

    // Same colour mapping as the status badge column on the table, so the tab and the badge agree.
    private const STATUS_COLOURS = [
        'Open' => 'info',
        'In Progress' => 'warning',
        'Closed' => 'success',
    ];

    /** I add a tab per ticket status so admins can jump straight to what needs a reply. */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        foreach (SupportTicket::STATUSES as $status) {
            $tabs[$status] = Tab::make($status)
                ->modifyQueryUsing(fn ($query) => $query->where('status', $status))
                ->badge(SupportTicket::where('status', $status)->count())
                ->badgeColor(self::STATUS_COLOURS[$status]);
        }

        return $tabs;
    }
}
