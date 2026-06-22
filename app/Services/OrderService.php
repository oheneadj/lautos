<?php

/**
 * Owns the order lifecycle — payment confirmation/rejection and shipment
 * stage progression. Filament actions call into this rather than mutating
 * the order directly, so the sequencing/logging rules can't be bypassed.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Events\OrderStageUpdated;
use App\Events\PaymentConfirmed;
use App\Events\PaymentRejected;
use App\Models\Car;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class OrderService
{
    /**
     * Places a new order for a car — creates the order in Pending Payment
     * and immediately reserves the car so it can't be double-booked while
     * payment is outstanding.
     */
    public function createOrder(User $user, Car $car): Order
    {
        if ($car->status !== CarStatus::Available) {
            throw new InvalidArgumentException('This car is no longer available to order.');
        }

        $order = Order::create([
            'user_id'                 => $user->id,
            'car_id'                  => $car->id,
            'status'                  => OrderStatus::PendingPayment,
            'price_usd_cents'         => $car->price_usd_cents,
            'shipping_cost_usd_cents' => $car->shipping_cost_usd_cents,
        ]);

        $car->update(['status' => CarStatus::Reserved]);

        $this->logHistory($order, OrderStatus::PendingPayment);

        OrderPlaced::dispatch($order);

        return $order;
    }

    /**
     * Confirms a customer's uploaded payment proof — moves the order to
     * Payment Confirmed and reserves the car.
     */
    public function confirmPayment(Order $order): void
    {
        if ($order->status !== OrderStatus::PaymentUploaded) {
            throw new InvalidArgumentException('Payment can only be confirmed while the order is in Payment Uploaded.');
        }

        $order->update(['status' => OrderStatus::PaymentConfirmed]);
        $order->car->update(['status' => CarStatus::Reserved]);

        $this->logHistory($order, OrderStatus::PaymentConfirmed);

        PaymentConfirmed::dispatch($order);
    }

    /**
     * Rejects a customer's uploaded payment proof — sends the order back to
     * Pending Payment so they can re-submit.
     */
    public function rejectPayment(Order $order, string $reason): void
    {
        if ($order->status !== OrderStatus::PaymentUploaded) {
            throw new InvalidArgumentException('Payment can only be rejected while the order is in Payment Uploaded.');
        }

        $order->update(['status' => OrderStatus::PendingPayment]);

        $this->logHistory($order, OrderStatus::PendingPayment, $reason);

        PaymentRejected::dispatch($order, $reason);
    }

    /**
     * Advances an order to the next stage in the shipment pipeline. Stages
     * must always move forward one at a time — skipping isn't allowed.
     *
     * @param  array{estimated_arrival_date?: string}  $data
     */
    public function advanceStage(Order $order, OrderStatus $toStage, array $data = []): void
    {
        $expectedNext = $order->status->next();

        if ($expectedNext === null || $toStage !== $expectedNext) {
            throw new InvalidArgumentException(
                "Orders must advance one stage at a time. Expected " . ($expectedNext?->label() ?? 'no further stages') . "."
            );
        }

        if ($toStage === OrderStatus::Shipped && empty($data['estimated_arrival_date'])) {
            throw new InvalidArgumentException('An estimated arrival date is required to advance to Shipped.');
        }

        $previousStatus = $order->status;

        $order->update(array_filter([
            'status' => $toStage,
            'estimated_arrival_date' => $data['estimated_arrival_date'] ?? null,
        ]));

        if ($toStage === OrderStatus::Delivered) {
            $order->update(['delivered_at' => now()]);
            $order->car->markSold();
        }

        $this->logHistory($order, $toStage);

        OrderStageUpdated::dispatch($order, $previousStatus);
    }

    private function logHistory(Order $order, OrderStatus $status, ?string $notes = null): void
    {
        $order->statusHistories()->create([
            'status' => $status,
            'changed_by' => Auth::id(),
            'notes' => $notes,
        ]);
    }
}
