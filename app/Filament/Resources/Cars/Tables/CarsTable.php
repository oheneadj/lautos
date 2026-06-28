<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Tables;

use App\Enums\CarBodyType;
use App\Enums\CarStatus;
use App\Models\Car;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images.0.path')
                    ->label('Photo')
                    ->disk('public')
                    ->square(),
                TextColumn::make('year')
                    ->sortable()
                    ->width('80px'),
                TextColumn::make('make.name')
                    ->label('Make')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('carModel.name')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('colour')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('transmission')
                    ->toggleable(),
                TextColumn::make('fuel_type')
                    ->toggleable(),
                TextColumn::make('mileage')
                    ->numeric()
                    ->sortable()
                    ->suffix(' km'),
                TextColumn::make('price_usd_cents')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2)),
                TextColumn::make('shipping_cost_usd_cents')
                    ->label('Shipping')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (CarStatus $state): string => $state->colour()),
                TextColumn::make('country_of_origin')
                    ->label('Origin')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('body_type')
                    ->label('Body Type')
                    ->formatStateUsing(fn (?CarBodyType $state): string => $state?->label() ?? '—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([15, 25, 50, 100])
            ->defaultPaginationPageOption(15)
            ->emptyStateIcon('heroicon-o-truck')
            ->emptyStateHeading('No cars listed yet')
            ->emptyStateDescription('Add your first car to start building the inventory.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Add first car')
                    ->icon('heroicon-m-plus'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CarStatus::class),
                SelectFilter::make('make_id')
                    ->label('Make')
                    ->relationship('make', 'name'),
                SelectFilter::make('fuel_type')
                    ->options(array_combine(Car::FUEL_TYPES, Car::FUEL_TYPES)),
                SelectFilter::make('transmission')
                    ->options(array_combine(Car::TRANSMISSIONS, Car::TRANSMISSIONS)),
                SelectFilter::make('country_of_origin')
                    ->label('Country of Origin')
                    ->options(array_combine(Car::COUNTRIES_OF_ORIGIN, Car::COUNTRIES_OF_ORIGIN)),
                SelectFilter::make('body_type')
                    ->label('Body Type')
                    ->options(CarBodyType::class),
                // I don't use TrashedFilter here — the ListCars "Archived" tab covers this,
                // and TrashedFilter's own default query would otherwise re-impose
                // withoutTrashed() on top of the tab's onlyTrashed(), cancelling it out.
            ])
            ->recordActions([
                // Inline status toggle right on the table, per US-07 — no need to open the
                // edit form just to mark a car Reserved or Sold.
                Action::make('changeStatus')
                    ->label('Change Status')
                    ->icon('heroicon-m-arrow-path')
                    ->modalWidth('sm')
                    ->button()
                    ->authorize('update')
                    ->schema(fn (Car $record) => [
                        Select::make('status')
                            ->options(CarStatus::class)
                            ->default($record->status)
                            ->required(),
                    ])
                    ->action(fn (Car $record, array $data) => $record->update(['status' => $data['status']])),
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->button(),
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->button(),
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->button(),
                RestoreAction::make()
                    ->label('Restore')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    // I let admins re-status many cars at once, e.g. marking a batch as Sold.
                    BulkAction::make('changeStatus')
                        ->label('Change Status')
                        ->icon('heroicon-m-arrow-path')
                        ->authorizeIndividualRecords('update')
                        ->schema([
                            Select::make('status')
                                ->options(CarStatus::class)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn (Car $car) => $car->update(['status' => $data['status']]));
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
