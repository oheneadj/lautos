<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Pages;

use App\Enums\CarStatus;
use App\Filament\Resources\Cars\CarResource;
use App\Models\Car;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCars extends ListRecords
{
    protected static string $resource = CarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * I add a tab per CarStatus so admins can jump straight to Available/Reserved/Sold,
     * plus the Archived tab for soft-deleted (auto-archived) cars.
     */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        foreach (CarStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->label())
                ->modifyQueryUsing(fn ($query) => $query->where('status', $status))
                ->badge(Car::where('status', $status)->count())
                ->badgeColor($status->colour());
        }

        // I return a brand new query here rather than mutating the one Filament passes in —
        // that one is a clone of the already-scoped base query, so the SoftDeletingScope's
        // "deleted_at IS NULL" where clause is already baked in by the time this closure
        // runs. Calling onlyTrashed() on it just adds a contradictory IS NOT NULL on top
        // and always returns zero rows. A fresh Car::onlyTrashed() avoids that entirely.
        $tabs['archived'] = Tab::make('Archived')
            ->modifyQueryUsing(fn () => Car::onlyTrashed());

        return $tabs;
    }
}
