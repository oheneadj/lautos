<?php

namespace Tests\Unit\Listeners;

use App\Enums\OrderStatus;
use App\Events\OrderStageUpdated;
use App\Listeners\SendReviewRequestNotification;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Notifications\ReviewRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests that the customer is prompted to leave a review the moment their
 * order reaches Delivered, and not at any other stage.
 */
class SendReviewRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(): Car
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
        ]);
    }

    #[Test]
    public function it_notifies_the_customer_when_the_order_is_delivered(): void
    {
        Notification::fake();

        $order = Order::factory()->create(['status' => OrderStatus::Delivered, 'car_id' => $this->makeCar()->id]);

        (new SendReviewRequestNotification())->handle(new OrderStageUpdated($order, OrderStatus::Cleared));

        Notification::assertSentTo($order->user, ReviewRequestNotification::class);
    }

    #[Test]
    public function it_does_nothing_for_any_other_stage_transition(): void
    {
        Notification::fake();

        $order = Order::factory()->create(['status' => OrderStatus::Shipped, 'car_id' => $this->makeCar()->id]);

        (new SendReviewRequestNotification())->handle(new OrderStageUpdated($order, OrderStatus::InTransitToPort));

        Notification::assertNothingSent();
    }
}
