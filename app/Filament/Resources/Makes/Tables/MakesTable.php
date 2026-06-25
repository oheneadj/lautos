<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Makes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MakesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon_path')
                    ->label('Icon')
                    ->disk('public')
                    ->width(32)
                    ->imageHeight(32),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cars_count')
                    ->label('Cars')
                    ->counts('cars')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('No makes added yet')
            ->emptyStateDescription('Add your first car make to start building the catalogue.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Add first make')
                    ->icon('heroicon-m-plus')
                    ->modalWidth('sm'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->color('info')
                    ->button()
                    ->icon('heroicon-m-pencil-square'),
                DeleteAction::make()
                    ->label('Delete')
                    ->color('danger')
                    ->button()
                    ->icon('heroicon-m-trash')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
