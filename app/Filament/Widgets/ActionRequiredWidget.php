<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActionRequiredWidget extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->can('ViewAny:Order') ?? false;
    }

    protected function getStats(): array
    {
        $count = Order::where('status', OrderStatus::PaymentUploaded)->count();

        return [
            Stat::make('Orders Requiring Action', $count)
                ->description('Awaiting payment confirmation')
                ->color($count > 0 ? 'warning' : 'success'),
        ];
    }
}
