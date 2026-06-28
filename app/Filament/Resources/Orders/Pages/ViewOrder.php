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
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

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
                Grid::make(1)
                    ->schema([
                        Section::make('Customer')
                            ->schema([
                                TextEntry::make('user.name')->label('Name'),
                                TextEntry::make('user.email')->label('Email'),
                                TextEntry::make('user.kyc_status')
                                    ->label('KYC Status')
                                    ->badge(),
                            ])->columns(2),
                        Section::make('Car')
                            ->schema([
                                TextEntry::make('car_year')->label('Year'),
                                TextEntry::make('car_make_name')->label('Make'),
                                TextEntry::make('car_model_name')->label('Model'),
                                TextEntry::make('total_usd_cents')
                                    ->label('Order Total')
                                    ->formatStateUsing(fn ($state) => '$'.number_format($state / 100, 2)),
                            ])->columns(3),
                    ]),
                Section::make('Shipment Timeline')
                    ->schema([
                        ViewEntry::make('timeline')
                            ->view('filament.infolists.order-timeline')
                            ->viewData(['order' => $this->getRecord()]),
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
                                : $this->competingOrdersCount($record).' other customers also have an open order on this car. Confirming this payment will cancel all of theirs.'),
                        TextEntry::make('vessel_name')
                            ->label('Vessel')
                            ->placeholder('Not yet provided')
                            ->visible(fn ($record) => $this->hasReachedLogisticsStage($record)),
                        TextEntry::make('tracking_number')
                            ->label('Tracking Number')
                            ->placeholder('Not yet provided')
                            ->visible(fn ($record) => $this->hasReachedLogisticsStage($record)),
                        TextEntry::make('estimated_arrival_date')
                            ->label('Est. Arrival Date')
                            ->date()
                            ->placeholder('Not yet provided')
                            ->visible(fn ($record) => $this->hasReachedLogisticsStage($record)),
                    ])
                    ->columns(3),

                Section::make('Payment Proofs')
                    ->schema([
                        ViewEntry::make('proofs')
                            ->view('filament.infolists.payment-proofs')
                            ->viewData(['order' => $this->getRecord()])->columnSpanFull(),
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

    /**
     * I gate the logistics fields/action on having reached In Transit to
     * Port — that's the earliest point a vessel/tracking number actually
     * exists, so showing this any earlier would just confuse the admin
     * about what stage the order is really at.
     */
    private function hasReachedLogisticsStage(Order $order): bool
    {
        return in_array($order->status, [
            OrderStatus::InTransitToPort,
            OrderStatus::Shipped,
            OrderStatus::ArrivedInGhana,
            OrderStatus::Cleared,
            OrderStatus::Delivered,
        ], true);
    }

    /**
     * Runs an OrderService call from one of the header actions, turning its
     * InvalidArgumentException (a state-guard the service throws on purpose,
     * e.g. confirming a payment that's already confirmed) into a friendly
     * notification instead of a raw 500 — the guard working as intended
     * shouldn't crash the page.
     */
    private function runOrderAction(Order $order, callable $callback): void
    {
        try {
            $callback();
        } catch (InvalidArgumentException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();

            return;
        }

        $this->redirect(static::getResource()::getUrl('view', ['record' => $order]));
    }

    protected function getHeaderActions(): array
    {
        $order = $this->getRecord();

        return [
            EditAction::make(),

            Action::make('confirmPayment')
                ->label('Confirm Payment')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn () => $order->status === OrderStatus::PaymentUploaded)
                ->authorize('update', $order)
                ->requiresConfirmation()
                ->action(fn () => $this->runOrderAction($order, fn () => app(OrderService::class)->confirmPayment($order))),

            Action::make('rejectPayment')
                ->label('Reject Payment')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn () => $order->status === OrderStatus::PaymentUploaded)
                ->authorize('update', $order)
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('reason')
                        ->label('Reason for rejection')
                        ->placeholder('e.g. The uploaded receipt does not match the order total.')
                        ->required(),
                ])
                ->action(fn (array $data) => $this->runOrderAction($order, fn () => app(OrderService::class)->rejectPayment($order, $data['reason']))),

            Action::make('advanceStage')
                ->label(fn () => 'Advance to '.($order->status->next()?->label() ?? '—'))
                ->icon('heroicon-m-arrow-right-circle')
                ->visible(fn () => $order->status !== OrderStatus::PaymentUploaded && $order->status->next() !== null)
                ->authorize('update', $order)
                ->requiresConfirmation()
                ->schema(fn () => $order->status->next() === OrderStatus::Shipped ? [
                    DatePicker::make('estimated_arrival_date')
                        ->label('Estimated Arrival Date')
                        ->placeholder('Select a date')
                        ->minDate(now())
                        ->required(),
                ] : [])
                ->action(fn (array $data) => $this->runOrderAction($order, fn () => app(OrderService::class)->advanceStage($order, $order->status->next(), $data))),

            Action::make('fillLogistics')
                ->label('Fill Logistics')
                ->icon('heroicon-m-truck')
                ->color('gray')
                ->modalWidth('sm')
                ->visible(fn () => $this->hasReachedLogisticsStage($order))
                ->authorize('update', $order)
                ->fillForm(fn () => $order->only(['vessel_name', 'tracking_number', 'estimated_arrival_date']))
                ->schema([
                    TextInput::make('vessel_name')
                        ->placeholder('e.g. MSC Olympia')
                        ->maxLength(100),
                    TextInput::make('tracking_number')
                        ->placeholder('e.g. MAEU123456789')
                        ->maxLength(100),
                ])
                ->action(function (array $data) use ($order) {
                    $order->update($data);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $order]));
                }),

            Action::make('cancelOrder')
                ->label('Cancel Order')
                ->icon('heroicon-m-no-symbol')
                ->color('danger')
                ->visible(fn () => ! in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Delivered], true))
                ->authorize('update', $order)
                ->requiresConfirmation()
                ->modalDescription('This releases the car back to Available if it was reserved for this order, and notifies the customer.')
                ->schema([
                    Textarea::make('reason')
                        ->label('Reason for cancellation')
                        ->placeholder('e.g. Customer requested cancellation.')
                        ->required(),
                ])
                ->action(fn (array $data) => $this->runOrderAction($order, fn () => app(OrderService::class)->cancelOrder($order, $data['reason']))),

            Action::make('addNote')
                ->label('Add Note')
                ->icon('heroicon-m-pencil')
                ->authorize('update', $order)
                ->schema([
                    Textarea::make('note')
                        ->label('Note')
                        ->placeholder('e.g. Customer called to confirm delivery address.')
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
