<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Faqs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->width('80px'),
                TextColumn::make('question')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('answer')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->emptyStateIcon('heroicon-o-question-mark-circle')
            ->emptyStateHeading('No FAQs added yet')
            ->emptyStateDescription('Add your first question to start building the FAQ page.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Add first FAQ')
                    ->icon('heroicon-m-plus'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->button(),
                DeleteAction::make()
                    ->label('Delete')
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
