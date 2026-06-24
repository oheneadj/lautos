<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * The admin's single screen for everything about one order — customer/KYC
 * summary, car, shipment timeline, payment proofs, internal notes, and
 * every action available for the order's current status.
 */
class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->getRecord())
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Customer')
                            ->schema([
                                TextEntry::make('user.name')->label('Name'),
                                TextEntry::make('user.email')->label('Email'),
                                TextEntry::make('user.kyc_status')
                                    ->label('KYC Status')
                                    ->badge(),
                            ]),
                        Section::make('Car')
                            ->schema([
                                TextEntry::make('car.year')->label('Year'),
                                TextEntry::make('car.make.name')->label('Make'),
                                TextEntry::make('car.carModel.name')->label('Model'),
                                TextEntry::make('total_usd_cents')
                                    ->label('Order Total')
                                    ->formatStateUsing(fn ($state) => '$' . number_format($state / 100, 2)),
                            ]),
                    ]),

                Section::make('Status')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Order Status')
                            ->badge()
                            ->color(fn (OrderStatus $state) => $state->colour()),
                        TextEntry::make('payment_status')
                            ->label('Payment Status')
                            ->badge(),
                        TextEntry::make('competing_orders')
                            ->label('Heads Up')
                            ->color('danger')
                            ->visible(fn ($record) => $this->competingOrdersCount($record) > 0)
                            ->state(fn ($record) => $this->competingOrdersCount($record) === 1
                                ? '1 other customer also has an open order on this car. Confirming this payment will cancel theirs.'
                                : $this->competingOrdersCount($record) . ' other customers also have an open order on this car. Confirming this payment will cancel all of theirs.'),
                    ])
                    ->columns(2),

                Section::make('Shipment Timeline')
                    ->schema([
                        ViewEntry::make('timeline')
                            ->view('filament.infolists.order-timeline')
                            ->viewData(['order' => $this->getRecord()]),
                    ]),

                Section::make('Payment Proofs')
                    ->schema([
                        ViewEntry::make('proofs')
                            ->view('filament.infolists.payment-proofs')
                            ->viewData(['order' => $this->getRecord()]),
                    ]),

                Section::make('Internal Notes')
                    ->description('Never visible to the customer.')
                    ->schema([
                        ViewEntry::make('notes')
                            ->view('filament.infolists.order-notes')
                            ->viewData(['order' => $this->getRecord()]),
                    ]),
            ]);
    }

    /**
     * Counts other open orders on the same car, so the admin sees — before
     * clicking confirm — that approving this payment will auto-cancel them.
     */
    private function competingOrdersCount(Order $order): int
    {
        return Order::where('car_id', $order->car_id)
            ->where('id', '!=', $order->id)
            ->whereIn('status', [OrderStatus::PendingPayment, OrderStatus::PaymentUploaded])
            ->count();
    }

    protected function getHeaderActions(): array
    {
        $order = $this->getRecord();

        return [
            Action::make('confirmPayment')
                ->label('Confirm Payment')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn () => $order->status === OrderStatus::PaymentUploaded)
                ->requiresConfirmation()
                ->action(function () use ($order) {
                    app(OrderService::class)->confirmPayment($order);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $order]));
                }),

            Action::make('rejectPayment')
                ->label('Reject Payment')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn () => $order->status === OrderStatus::PaymentUploaded)
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('reason')
                        ->label('Reason for rejection')
                        ->required(),
                ])
                ->action(function (array $data) use ($order) {
                    app(OrderService::class)->rejectPayment($order, $data['reason']);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $order]));
                }),

            Action::make('advanceStage')
                ->label(fn () => 'Advance to ' . ($order->status->next()?->label() ?? '—'))
                ->icon('heroicon-m-arrow-right-circle')
                ->visible(fn () => $order->status !== OrderStatus::PaymentUploaded && $order->status->next() !== null)
                ->requiresConfirmation()
                ->schema(fn () => $order->status->next() === OrderStatus::Shipped ? [
                    DatePicker::make('estimated_arrival_date')
                        ->label('Estimated Arrival Date')
                        ->required(),
                ] : [])
                ->action(function (array $data) use ($order) {
                    app(OrderService::class)->advanceStage($order, $order->status->next(), $data);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $order]));
                }),

            Action::make('cancelOrder')
                ->label('Cancel Order')
                ->icon('heroicon-m-no-symbol')
                ->color('danger')
                ->visible(fn () => ! in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Delivered], true))
                ->requiresConfirmation()
                ->modalDescription('This releases the car back to Available if it was reserved for this order, and notifies the customer.')
                ->schema([
                    Textarea::make('reason')
                        ->label('Reason for cancellation')
                        ->required(),
                ])
                ->action(function (array $data) use ($order) {
                    app(OrderService::class)->cancelOrder($order, $data['reason']);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $order]));
                }),

            Action::make('addNote')
                ->label('Add Note')
                ->icon('heroicon-m-pencil')
                ->schema([
                    Textarea::make('note')
                        ->label('Note')
                        ->required(),
                ])
                ->action(function (array $data) use ($order) {
                    $order->notes()->create([
                        'admin_id' => Auth::id(),
                        'note' => $data['note'],
                    ]);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $order]));
                }),
        ];
    }
}
