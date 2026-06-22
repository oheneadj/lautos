<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Widgets;

use App\Enums\CarStatus;
use App\Models\Car;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentCarsTable extends TableWidget
{
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return auth()->user()?->can('ViewAny:Car') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recently Added Cars')
            ->query(Car::query()->latest()->limit(5))
            ->columns([
                ImageColumn::make('images.0.path')
                    ->label('Photo')
                    ->disk('public')
                    ->square(),
                TextColumn::make('year'),
                TextColumn::make('make.name')->label('Make'),
                TextColumn::make('carModel.name')->label('Model'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (CarStatus $state) => $state->colour()),
            ])
            ->paginated(false);
    }
}
