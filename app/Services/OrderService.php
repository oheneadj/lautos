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
use App\Enums\PaymentProofStatus;
use App\Events\OrderCancelledByAdmin;
use App\Events\OrderPlaced;
use App\Events\OrderStageUpdated;
use App\Events\PaymentConfirmed;
use App\Events\PaymentRejected;
use App\Events\ReservationLost;
use App\Models\Car;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    /**
     * Places a new order for a car. I don't lock the car here — only a
     * confirmed payment does that (see confirmPayment()). Locking on order
     * placement let a customer with no intent to pay block the car forever,
     * since there was no way to recover it. Multiple customers can have an
     * open order on the same car at once; the first one whose payment gets
     * confirmed wins it.
     */
    public function createOrder(User $user, Car $car): Order
    {
        if ($car->status !== CarStatus::Available) {
            throw new InvalidArgumentException('This car is no longer available to order.');
        }

        // I reuse an existing open order rather than letting the same
        // customer pile up duplicates on the same car.
        $existing = $car->orders()
            ->where('user_id', $user->id)
            ->whereIn('status', [OrderStatus::PendingPayment, OrderStatus::PaymentUploaded])
            ->first();

        if ($existing) {
            return $existing;
        }

        // I snapshot the car's identity here too — same reason as the price
        // fields below — so a later edit or deletion of the car never
        // changes how this order displays.
        $order = Order::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => OrderStatus::PendingPayment,
            'car_year' => $car->year,
            'car_make_name' => $car->make->name,
            'car_model_name' => $car->carModel->name,
            'car_thumbnail_path' => $car->images->first()?->path,
            'price_usd_cents' => $car->price_usd_cents,
            'shipping_cost_usd_cents' => $car->shipping_cost_usd_cents,
        ]);

        $this->logHistory($order, OrderStatus::PendingPayment);

        OrderPlaced::dispatch($order);

        return $order;
    }

    /**
     * Confirms a customer's uploaded payment proof — moves the order to
     * Payment Confirmed, reserves the car, and cancels every other open
     * order on the same car since the race for it is now over.
     */
    public function confirmPayment(Order $order): void
    {
        // I lock the row and re-check status inside the transaction so two
        // near-simultaneous confirms (a double-click, or two admin sessions
        // on the same order) can't both pass the check before either writes
        // — without this, the order could get double-logged, the event
        // double-fired, and cancelCompetingOrders() run from two "winners".
        DB::transaction(function () use ($order) {
            $locked = Order::lockForUpdate()->findOrFail($order->id);

            if ($locked->status !== OrderStatus::PaymentUploaded) {
                throw new InvalidArgumentException('Payment can only be confirmed while the order is in Payment Uploaded.');
            }

            $locked->update(['status' => OrderStatus::PaymentConfirmed]);
            $locked->car->update(['status' => CarStatus::Reserved]);

            // The latest upload is the one the admin just reviewed — everything
            // before it was already resolved (accepted or rejected) on a prior pass.
            $locked->paymentProofs()->first()?->update(['status' => PaymentProofStatus::Accepted]);

            $this->logHistory($locked, OrderStatus::PaymentConfirmed);

            PaymentConfirmed::dispatch($locked);

            $this->cancelCompetingOrders($locked);
        });
    }

    /**
     * Cancels every other open order on the winning order's car — they lost
     * the race the moment this customer's payment was confirmed.
     */
    private function cancelCompetingOrders(Order $winner): void
    {
        Order::where('car_id', $winner->car_id)
            ->where('id', '!=', $winner->id)
            ->whereIn('status', [OrderStatus::PendingPayment, OrderStatus::PaymentUploaded])
            ->get()
            ->each(function (Order $order) {
                $order->update(['status' => OrderStatus::Cancelled]);
                $this->logHistory($order, OrderStatus::Cancelled, 'Another buyer completed payment for this car first.');
                ReservationLost::dispatch($order);
            });
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

        $order->paymentProofs()->first()?->update(['status' => PaymentProofStatus::Rejected]);

        $this->logHistory($order, OrderStatus::PendingPayment, $reason);

        PaymentRejected::dispatch($order, $reason);
    }

    /**
     * Cancels an order at any point before delivery — e.g. the customer
     * backed out after paying, or a refund was issued. A confirmed payment
     * reserves the car (see confirmPayment()), and nothing else releases
     * it back to Available, so without this a cancelled-after-confirmation
     * order would leave the car permanently locked for every other buyer.
     */
    public function cancelOrder(Order $order, string $reason): void
    {
        if (in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Delivered], true)) {
            throw new InvalidArgumentException('This order can no longer be cancelled.');
        }

        $order->update(['status' => OrderStatus::Cancelled]);

        // Only release the car if it was reserved for THIS order — a car is
        // only ever Reserved on behalf of whichever order's payment was
        // confirmed, so if that's this one, cancelling it frees the car up again.
        if ($order->car->status === CarStatus::Reserved) {
            $order->car->update(['status' => CarStatus::Available]);
        }

        $this->logHistory($order, OrderStatus::Cancelled, $reason);

        OrderCancelledByAdmin::dispatch($order, $reason);
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
                'Orders must advance one stage at a time. Expected '.($expectedNext?->label() ?? 'no further stages').'.'
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
