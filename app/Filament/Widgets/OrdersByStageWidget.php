<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\BarChartWidget;

class OrdersByStageWidget extends BarChartWidget
{
    protected ?string $heading = 'Orders by Stage';

    public static function canView(): bool
    {
        return auth()->user()?->can('ViewAny:Order') ?? false;
    }

    protected function getData(): array
    {
        $stages = OrderStatus::pipeline();

        $counts = collect($stages)->map(
            fn (OrderStatus $stage) => Order::where('status', $stage)->count()
        );

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $counts->all(),
                ],
            ],
            'labels' => collect($stages)->map(fn (OrderStatus $stage) => $stage->label())->all(),
        ];
    }
}
