<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Pages;

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
     * I add an Archived tab so admins can see soft-deleted (auto-archived) cars
     * without it cluttering the default listing.
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            // I return a brand new query here rather than mutating the one Filament passes in —
            // that one is a clone of the already-scoped base query, so the SoftDeletingScope's
            // "deleted_at IS NULL" where clause is already baked in by the time this closure
            // runs. Calling onlyTrashed() on it just adds a contradictory IS NOT NULL on top
            // and always returns zero rows. A fresh Car::onlyTrashed() avoids that entirely.
            'archived' => Tab::make('Archived')
                ->modifyQueryUsing(fn () => Car::onlyTrashed()),
        ];
    }
}
