<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Reviews\Tables;

use App\Enums\ReviewStatus;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.car.make.name')
                    ->label('Car')
                    ->formatStateUsing(
                        fn ($record) => "{$record->order?->car?->year} {$record->order?->car?->make?->name} {$record->order?->car?->carModel?->name}"
                    ),
                TextColumn::make('rating')
                    ->badge()
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color('warning'),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('body')
                    ->label('Review')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (ReviewStatus $state): string => $state->colour())
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-star')
            ->emptyStateHeading('No reviews yet')
            ->emptyStateDescription('Customer reviews will appear here once delivered orders are reviewed.')
            ->filters([
                SelectFilter::make('status')
                    ->options(ReviewStatus::class),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (Review $record) => $record->status !== ReviewStatus::Approved)
                    ->action(function (Review $record) {
                        $record->update(['status' => ReviewStatus::Approved, 'approved_at' => now()]);
                        Notification::make()->title('Review approved')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record) => $record->status !== ReviewStatus::Rejected)
                    ->action(function (Review $record) {
                        $record->update(['status' => ReviewStatus::Rejected, 'approved_at' => null]);
                        Notification::make()->title('Review rejected')->success()->send();
                    }),
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-m-trash'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
