<?php

namespace Tests\Unit\Services;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Events\OrderStageUpdated;
use App\Events\PaymentConfirmed;
use App\Events\PaymentRejected;
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
    public function placing_an_order_creates_it_pending_and_reserves_the_car(): void
    {
        Event::fake([OrderPlaced::class]);

        $car = $this->makeAvailableCar();
        $user = User::factory()->create();

        $order = (new OrderService())->createOrder($user, $car);

        $this->assertSame(OrderStatus::PendingPayment, $order->status);
        $this->assertSame(1500000, $order->price_usd_cents);
        $this->assertSame(200000, $order->shipping_cost_usd_cents);
        $this->assertSame(CarStatus::Reserved, $car->refresh()->status);
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
