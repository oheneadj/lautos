<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /** I add a tab per OrderStatus so admins can jump straight to a given pipeline stage. */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        // I count every status in one grouped query instead of one count() per
        // tab, so this page doesn't run 9 separate COUNT queries on every load.
        $counts = Order::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        foreach (OrderStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->label())
                ->modifyQueryUsing(fn ($query) => $query->where('status', $status))
                ->badge($counts[$status->value] ?? 0)
                ->badgeColor($status->colour());
        }

        return $tabs;
    }
}
