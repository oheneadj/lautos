<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Widgets;

use App\Enums\CarStatus;
use App\Models\Car;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CarStatsWidget extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->can('ViewAny:Car') ?? false;
    }

    protected function getStats(): array
    {
        // I count across all cars including soft-deleted (archived sold) ones, since
        // "Total Sold Cars (all time)" should include cars that have since been archived.
        return [
            Stat::make('Available Cars', Car::withTrashed()->where('status', CarStatus::Available)->count())
                ->color('success'),
            Stat::make('Reserved Cars', Car::withTrashed()->where('status', CarStatus::Reserved)->count())
                ->color('warning'),
            Stat::make('Sold Cars (All Time)', Car::withTrashed()->where('status', CarStatus::Sold)->count())
                ->color('danger'),
        ];
    }
}
