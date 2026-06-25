<?php

/**
 * Lets an admin manage a make's models — and each model's trims — from the
 * make's own edit page, instead of only being able to create them on the fly
 * from the car form's "+" buttons.
 *
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Makes\RelationManagers;

use App\Models\CarModel;
use App\Services\CarModelService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarModelsRelationManager extends RelationManager
{
    protected static string $relationship = 'carModels';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->placeholder('e.g. Corolla')
                ->maxLength(100),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('trims_count')->label('Trims')->counts('trims'),
                TextColumn::make('cars_count')->label('Cars')->counts('cars'),
            ])
            ->defaultSort('name')
            ->headerActions([
                CreateAction::make()->modalWidth('sm'),
            ])
            ->recordActions([
                $this->manageTrimsAction(),
                EditAction::make()->modalWidth('sm'),
                DeleteAction::make(),
            ]);
    }

    /**
     * I manage trims through a Repeater rather than a nested relation manager
     * — Filament can't nest a relation manager inside another relation
     * manager's row, and trims only ever have a single field (name), so a
     * Repeater modal is simpler than a whole extra page would be.
     */
    private function manageTrimsAction(): Action
    {
        return Action::make('manageTrims')
            ->label('Manage Trims')
            ->icon('heroicon-m-tag')
            ->color('warning')
            ->button()
            ->modalWidth('md')
            ->fillForm(fn (CarModel $record) => [
                'trims' => $record->trims->map(fn ($trim) => ['name' => $trim->name])->all(),
            ])
            ->schema([
                Repeater::make('trims')
                    ->label('Trims')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->placeholder('e.g. Sport, LE, XLE, SE')
                            ->maxLength(100),
                    ])
                    ->addActionLabel('Add Trim')
                    ->reorderable(false)
                    ->defaultItems(0),
            ])
            ->action(function (array $data, CarModel $record) {
                app(CarModelService::class)->syncTrims($record, array_column($data['trims'], 'name'));
            });
    }
}
