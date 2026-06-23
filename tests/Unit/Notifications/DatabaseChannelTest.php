<?php

namespace Tests\Unit\Notifications;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\User;
use App\Notifications\KycResubmissionRequestedNotification;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderStageUpdatedNotification;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentProofReceivedNotification;
use App\Notifications\PaymentRejectedNotification;
use App\Notifications\ReservationLostNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Confirms the customer-facing order/KYC notifications write to the
 * database channel (not just mail) so they actually show up on the
 * dashboard's notifications page (US-46 / Epic 21).
 */
class DatabaseChannelTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(): Order
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        return Order::factory()->create(['car_id' => $car->id]);
    }

    #[Test]
    public function order_placed_notification_writes_to_the_database_channel(): void
    {
        $order = $this->makeOrder();
        $order->user->notify(new OrderPlacedNotification($order));

        $this->assertDatabaseCount('notifications', 1);
        $this->assertSame(1, $order->user->unreadNotifications()->count());
    }

    #[Test]
    public function order_stage_updated_notification_writes_to_the_database_channel(): void
    {
        $order = $this->makeOrder();
        $order->user->notify(new OrderStageUpdatedNotification($order));

        $this->assertSame(1, $order->user->unreadNotifications()->count());
    }

    #[Test]
    public function payment_confirmed_notification_writes_to_the_database_channel(): void
    {
        $order = $this->makeOrder();
        $order->user->notify(new PaymentConfirmedNotification($order));

        $this->assertSame(1, $order->user->unreadNotifications()->count());
    }

    #[Test]
    public function payment_proof_received_notification_writes_to_the_database_channel(): void
    {
        $order = $this->makeOrder();
        $order->user->notify(new PaymentProofReceivedNotification($order));

        $this->assertSame(1, $order->user->unreadNotifications()->count());
    }

    #[Test]
    public function payment_rejected_notification_writes_to_the_database_channel(): void
    {
        $order = $this->makeOrder();
        $order->user->notify(new PaymentRejectedNotification($order, 'Illegible receipt'));

        $notification = $order->user->unreadNotifications()->first();
        $this->assertSame('Illegible receipt', explode('Reason: ', $notification->data['message'])[1]);
    }

    #[Test]
    public function reservation_lost_notification_writes_to_the_database_channel(): void
    {
        $order = $this->makeOrder();
        $order->user->notify(new ReservationLostNotification($order));

        $notification = $order->user->unreadNotifications()->first();
        $this->assertSame('Car No Longer Available', $notification->data['title']);
    }

    #[Test]
    public function kyc_resubmission_notification_writes_to_the_database_channel(): void
    {
        $user = User::factory()->create();
        $user->notify(new KycResubmissionRequestedNotification('Blurry photo'));

        $notification = $user->unreadNotifications()->first();
        $this->assertSame('document', $notification->data['icon']);
        $this->assertStringContainsString('Blurry photo', $notification->data['message']);
    }
}
