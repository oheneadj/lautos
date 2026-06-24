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
use App\Notifications\ReviewRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Confirms each customer-facing notification adds 'giantsms' to its via()
 * channels when the user has a phone number, and leaves it out when they
 * don't — the channel itself would skip a phoneless send anyway, but
 * there's no reason to even queue a no-op SMS job.
 */
class SmsChannelInclusionTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(?string $phone): Order
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
        $user = User::factory()->create(['phone' => $phone]);

        return Order::factory()->for($user)->create(['car_id' => $car->id]);
    }

    public static function notificationProvider(): array
    {
        return [
            'OrderPlacedNotification' => [fn (Order $order) => new OrderPlacedNotification($order)],
            'PaymentConfirmedNotification' => [fn (Order $order) => new PaymentConfirmedNotification($order)],
            'PaymentProofReceivedNotification' => [fn (Order $order) => new PaymentProofReceivedNotification($order)],
            'PaymentRejectedNotification' => [fn (Order $order) => new PaymentRejectedNotification($order, 'Blurry receipt')],
            'OrderStageUpdatedNotification' => [fn (Order $order) => new OrderStageUpdatedNotification($order)],
            'ReservationLostNotification' => [fn (Order $order) => new ReservationLostNotification($order)],
            'ReviewRequestNotification' => [fn (Order $order) => new ReviewRequestNotification($order)],
        ];
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('notificationProvider')]
    public function it_includes_giantsms_when_the_user_has_a_phone_number(callable $make): void
    {
        $order = $this->makeOrder('0551234567');

        $channels = $make($order)->via($order->user);

        $this->assertContains('giantsms', $channels);
        $this->assertContains('mail', $channels);
        $this->assertContains('database', $channels);
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('notificationProvider')]
    public function it_excludes_giantsms_when_the_user_has_no_phone_number(callable $make): void
    {
        $order = $this->makeOrder(null);

        $channels = $make($order)->via($order->user);

        $this->assertNotContains('giantsms', $channels);
    }

    #[Test]
    public function kyc_resubmission_notification_includes_giantsms_when_the_user_has_a_phone_number(): void
    {
        $user = User::factory()->create(['phone' => '0551234567']);

        $channels = (new KycResubmissionRequestedNotification('Blurry photo'))->via($user);

        $this->assertContains('giantsms', $channels);
    }

    #[Test]
    public function kyc_resubmission_notification_excludes_giantsms_when_the_user_has_no_phone_number(): void
    {
        $user = User::factory()->create(['phone' => null]);

        $channels = (new KycResubmissionRequestedNotification('Blurry photo'))->via($user);

        $this->assertNotContains('giantsms', $channels);
    }
}
