<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('Order #')
                    ->searchable()
                    ->copyable()
                    ->limit(8)
                    ->tooltip(fn ($record) => $record->uuid),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('car.make.name')
                    ->label('Car')
                    ->formatStateUsing(
                        fn ($record) => "{$record->car?->year} {$record->car?->make?->name} {$record->car?->carModel?->name}"
                    )
                    ->searchable(query: fn (Builder $query, string $search) => $query
                        ->orWhereHas('car.make', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('car.carModel', fn ($q) => $q->where('name', 'like', "%{$search}%"))),
                TextColumn::make('status')
                    ->label('Order Status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => $state->colour())
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Uploaded' => 'info',
                        'Confirmed' => 'success',
                        'Cancelled' => 'danger',
                    }),
                TextColumn::make('price_usd_cents')
                    ->label('Total')
                    ->formatStateUsing(fn ($record) => '$' . number_format($record->total_usd_cents / 100, 2))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Placed')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            // I highlight Payment Uploaded rows so admins immediately see which orders need action.
            ->recordClasses(fn (Order $record) => $record->status === OrderStatus::PaymentUploaded
                ? 'bg-warning-50 dark:bg-warning-400/10'
                : null)
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateHeading('No orders yet')
            ->emptyStateDescription('Orders will appear here once customers place them.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Create first order')
                    ->icon('heroicon-m-plus'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Order Status')
                    ->options(OrderStatus::class),
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Uploaded' => 'Uploaded',
                        'Confirmed' => 'Confirmed',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        return match ($value) {
                            'Pending' => $query->where('status', OrderStatus::PendingPayment),
                            'Uploaded' => $query->where('status', OrderStatus::PaymentUploaded),
                            'Cancelled' => $query->where('status', OrderStatus::Cancelled),
                            'Confirmed' => $query->whereNotIn('status', [OrderStatus::PendingPayment, OrderStatus::PaymentUploaded, OrderStatus::Cancelled]),
                            default => $query,
                        };
                    }),
                Filter::make('placed_between')
                    ->schema([
                        DatePicker::make('placed_from'),
                        DatePicker::make('placed_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['placed_from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['placed_until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->button(),
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
