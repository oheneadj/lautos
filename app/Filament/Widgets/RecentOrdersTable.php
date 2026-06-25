<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentOrdersTable extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('ViewAny:Order') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Orders')
            // I eager-load these so the Customer/Car/Make/Model columns below don't
            // each trigger their own query per row.
            ->query(Order::query()->with(['user', 'car.make', 'car.carModel'])->latest()->limit(10))
            ->columns([
                TextColumn::make('user.name')->label('Customer'),
                TextColumn::make('car.make.name')
                    ->label('Car')
                    ->formatStateUsing(fn ($record) => "{$record->car?->year} {$record->car?->make?->name} {$record->car?->carModel?->name}"),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state) => $state->colour()),
                TextColumn::make('created_at')->label('Placed')->dateTime(),
            ])
            ->paginated(false);
    }
}
