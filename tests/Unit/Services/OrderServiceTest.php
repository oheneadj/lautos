<?php

namespace Tests\Unit\Services;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Events\OrderStageUpdated;
use App\Events\PaymentConfirmed;
use App\Events\PaymentRejected;
use App\Events\ReservationLost;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the order lifecycle rules — payment confirm/reject and sequential
 * shipment stage progression (US-12 / US-13).
 */
class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    // I create the car explicitly with a known make/model rather than letting Order::factory()'s
    // nested Car::factory() pick a random make_id from an empty table, which crashes the Car
    // model's slug-generation hook when it tries to read $car->make->name on a null relation.
    private function makeOrder(array $attributes = []): Order
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        return Order::factory()->create(array_merge(['car_id' => $car->id], $attributes));
    }

    private function makeAvailableCar(): Car
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => CarStatus::Available,
            'price_usd_cents' => 1500000,
            'shipping_cost_usd_cents' => 200000,
        ]);
    }

    #[Test]
    public function placing_an_order_creates_it_pending_without_locking_the_car(): void
    {
        Event::fake([OrderPlaced::class]);

        $car = $this->makeAvailableCar();
        $user = User::factory()->create();

        $order = (new OrderService())->createOrder($user, $car);

        $this->assertSame(OrderStatus::PendingPayment, $order->status);
        $this->assertSame(1500000, $order->price_usd_cents);
        $this->assertSame(200000, $order->shipping_cost_usd_cents);
        // The car only locks once payment is confirmed — see confirmPayment() tests below.
        // Placing an order must never block other customers from also trying to buy it.
        $this->assertSame(CarStatus::Available, $car->refresh()->status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::PendingPayment->value,
        ]);
        Event::assertDispatched(OrderPlaced::class);
    }

    #[Test]
    public function an_already_reserved_car_cannot_be_ordered_again(): void
    {
        $car = $this->makeAvailableCar();
        $car->update(['status' => CarStatus::Reserved]);
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);

        (new OrderService())->createOrder($user, $car);
    }

    #[Test]
    public function two_different_customers_can_both_have_an_open_order_on_the_same_car(): void
    {
        Event::fake([OrderPlaced::class]);

        $car = $this->makeAvailableCar();
        $firstBuyer = User::factory()->create();
        $secondBuyer = User::factory()->create();

        $service = new OrderService();
        $firstOrder = $service->createOrder($firstBuyer, $car);
        $secondOrder = $service->createOrder($secondBuyer, $car);

        $this->assertNotSame($firstOrder->id, $secondOrder->id);
        $this->assertSame(CarStatus::Available, $car->refresh()->status);
    }

    #[Test]
    public function placing_a_second_order_on_the_same_car_reuses_the_existing_open_order(): void
    {
        Event::fake([OrderPlaced::class]);

        $car = $this->makeAvailableCar();
        $user = User::factory()->create();

        $service = new OrderService();
        $firstOrder = $service->createOrder($user, $car);
        $secondAttempt = $service->createOrder($user, $car);

        $this->assertSame($firstOrder->id, $secondAttempt->id);
        $this->assertSame(1, Order::where('user_id', $user->id)->where('car_id', $car->id)->count());
    }

    #[Test]
    public function confirming_one_buyers_payment_cancels_every_other_open_order_on_the_same_car(): void
    {
        Event::fake([PaymentConfirmed::class, ReservationLost::class]);

        $car = $this->makeAvailableCar();
        $winner = User::factory()->create();
        $loser = User::factory()->create();

        $service = new OrderService();
        $winningOrder = $service->createOrder($winner, $car);
        $losingOrder = $service->createOrder($loser, $car);

        $winningOrder->update(['status' => OrderStatus::PaymentUploaded]);
        $service->confirmPayment($winningOrder);

        $this->assertSame(OrderStatus::PaymentConfirmed, $winningOrder->refresh()->status);
        $this->assertSame(CarStatus::Reserved, $car->refresh()->status);
        $this->assertSame(OrderStatus::Cancelled, $losingOrder->refresh()->status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $losingOrder->id,
            'status' => OrderStatus::Cancelled->value,
            'notes' => 'Another buyer completed payment for this car first.',
        ]);
        Event::assertDispatched(ReservationLost::class, fn ($event) => $event->order->id === $losingOrder->id);
    }

    #[Test]
    public function confirming_payment_moves_the_order_forward_and_reserves_the_car(): void
    {
        // I only fake the specific event I'm asserting on — faking all events also suppresses
        // Eloquent's internal model events, which is what generates User's uuid on creation.
        Event::fake([PaymentConfirmed::class]);

        $order = $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);

        (new OrderService())->confirmPayment($order);

        $this->assertSame(OrderStatus::PaymentConfirmed, $order->refresh()->status);
        $this->assertSame(CarStatus::Reserved, $order->car->refresh()->status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::PaymentConfirmed->value,
        ]);
        Event::assertDispatched(PaymentConfirmed::class);
    }

    #[Test]
    public function confirming_payment_outside_payment_uploaded_is_rejected(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PendingPayment]);

        $this->expectException(InvalidArgumentException::class);

        (new OrderService())->confirmPayment($order);
    }

    #[Test]
    public function rejecting_payment_sends_the_order_back_to_pending_with_a_reason(): void
    {
        Event::fake([PaymentRejected::class]);

        $order = $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);

        (new OrderService())->rejectPayment($order, 'Amount does not match invoice.');

        $this->assertSame(OrderStatus::PendingPayment, $order->refresh()->status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::PendingPayment->value,
            'notes' => 'Amount does not match invoice.',
        ]);
        Event::assertDispatched(PaymentRejected::class);
    }

    #[Test]
    public function cancelling_a_confirmed_order_releases_the_reserved_car(): void
    {
        Event::fake([\App\Events\OrderCancelledByAdmin::class]);

        $car = $this->makeAvailableCar();
        $order = Order::factory()->create(['car_id' => $car->id, 'status' => OrderStatus::PaymentConfirmed]);
        $car->update(['status' => CarStatus::Reserved]);

        (new OrderService())->cancelOrder($order, 'Customer requested a refund.');

        $this->assertSame(OrderStatus::Cancelled, $order->refresh()->status);
        $this->assertSame(CarStatus::Available, $car->refresh()->status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::Cancelled->value,
            'notes' => 'Customer requested a refund.',
        ]);
        Event::assertDispatched(\App\Events\OrderCancelledByAdmin::class, fn ($event) => $event->order->id === $order->id);
    }

    #[Test]
    public function cancelling_an_order_whose_car_is_not_reserved_does_not_touch_the_car(): void
    {
        Event::fake([\App\Events\OrderCancelledByAdmin::class]);

        $order = $this->makeOrder(['status' => OrderStatus::PendingPayment]);
        $order->car->update(['status' => CarStatus::Available]);

        (new OrderService())->cancelOrder($order, 'Customer changed their mind.');

        $this->assertSame(OrderStatus::Cancelled, $order->refresh()->status);
        $this->assertSame(CarStatus::Available, $order->car->refresh()->status);
    }

    #[Test]
    public function an_already_cancelled_order_cannot_be_cancelled_again(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::Cancelled]);

        $this->expectException(InvalidArgumentException::class);

        (new OrderService())->cancelOrder($order, 'Already cancelled.');
    }

    #[Test]
    public function a_delivered_order_cannot_be_cancelled(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::Delivered]);

        $this->expectException(InvalidArgumentException::class);

        (new OrderService())->cancelOrder($order, 'Too late.');
    }

    #[Test]
    public function advancing_a_stage_out_of_sequence_is_rejected(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PendingPayment]);

        $this->expectException(InvalidArgumentException::class);

        // Skips Payment Uploaded and Payment Confirmed entirely.
        (new OrderService())->advanceStage($order, OrderStatus::Purchased);
    }

    #[Test]
    public function advancing_to_shipped_requires_an_estimated_arrival_date(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::InTransitToPort]);

        $this->expectException(InvalidArgumentException::class);

        (new OrderService())->advanceStage($order, OrderStatus::Shipped);
    }

    #[Test]
    public function advancing_to_shipped_with_a_date_succeeds(): void
    {
        Event::fake([OrderStageUpdated::class]);

        $order = $this->makeOrder(['status' => OrderStatus::InTransitToPort]);

        (new OrderService())->advanceStage($order, OrderStatus::Shipped, [
            'estimated_arrival_date' => now()->addWeeks(3)->toDateString(),
        ]);

        $order->refresh();
        $this->assertSame(OrderStatus::Shipped, $order->status);
        $this->assertNotNull($order->estimated_arrival_date);
        Event::assertDispatched(OrderStageUpdated::class);
    }

    #[Test]
    public function advancing_to_delivered_marks_the_car_sold_and_records_delivered_at(): void
    {
        Event::fake([OrderStageUpdated::class]);

        $order = $this->makeOrder(['status' => OrderStatus::Cleared]);

        (new OrderService())->advanceStage($order, OrderStatus::Delivered);

        $order->refresh();
        $this->assertSame(OrderStatus::Delivered, $order->status);
        $this->assertNotNull($order->delivered_at);
        $this->assertSame(CarStatus::Sold, $order->car->refresh()->status);
        $this->assertNotNull($order->car->sold_at);
    }

    #[Test]
    public function every_stage_advance_is_logged_to_history(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PaymentConfirmed]);

        (new OrderService())->advanceStage($order, OrderStatus::Purchased);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::Purchased->value,
        ]);
    }
}
