<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Services\OrderService;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $order = $this->getRecord();

        return [
            ViewAction::make(),

            // I duplicate the ViewOrder page's cancelOrder action here so an
            // admin doesn't have to leave the edit form to cancel an order.
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
                        ->placeholder('e.g. Customer requested cancellation.')
                        ->required(),
                ])
                ->action(function (array $data) use ($order) {
                    app(OrderService::class)->cancelOrder($order, $data['reason']);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $order]));
                }),

            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
