<?php

namespace Tests\Unit\Notifications;

use App\Enums\OrderStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\Setting;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderStageUpdatedNotification;
use App\Notifications\PaymentProofReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the content of the order-related notification emails (US-45 / SRS 5.6).
 */
class OrderNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(array $attributes = []): Order
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        return Order::factory()->create(array_merge(['car_id' => $car->id], $attributes));
    }

    #[Test]
    public function the_order_reference_is_a_short_human_quotable_code(): void
    {
        $order = $this->makeOrder();

        $this->assertMatchesRegularExpression('/^LA-[A-F0-9]{8}$/', $order->reference);
    }

    #[Test]
    public function every_order_email_includes_the_order_reference(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::Purchased]);
        $mail = (new OrderStageUpdatedNotification($order))->toMail($order->user);

        $this->assertStringContainsString($order->reference, implode(' ', $mail->introLines));
    }

    #[Test]
    public function the_arrived_in_ghana_email_includes_the_demurrage_warning(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::ArrivedInGhana]);
        $mail = (new OrderStageUpdatedNotification($order))->toMail($order->user);

        $this->assertStringContainsString('demurrage', implode(' ', $mail->introLines));
    }

    #[Test]
    public function other_stages_do_not_include_the_demurrage_warning(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::Purchased]);
        $mail = (new OrderStageUpdatedNotification($order))->toMail($order->user);

        $this->assertStringNotContainsString('demurrage', implode(' ', $mail->introLines));
    }

    #[Test]
    public function the_payment_proof_received_email_acknowledges_review(): void
    {
        $order = $this->makeOrder();
        $mail = (new PaymentProofReceivedNotification($order))->toMail($order->user);

        $this->assertStringContainsString("We're reviewing it now", implode(' ', $mail->introLines));
    }

    #[Test]
    public function the_order_placed_email_includes_the_real_bank_account_details(): void
    {
        // The bank/account lines previously read 'account_name'/'account_number'
        // from Setting, but the Settings page actually stores them under
        // 'bank_account_name'/'bank_account_number' — so this line always
        // rendered the '—' fallback no matter what the admin had configured.
        Setting::set('bank_account_name', 'Livingston Autos Ltd');
        Setting::set('bank_account_number', '1234567890');

        $order = $this->makeOrder();
        $mail = (new OrderPlacedNotification($order))->toMail($order->user);

        $line = implode(' ', $mail->introLines);
        $this->assertStringContainsString('Livingston Autos Ltd', $line);
        $this->assertStringContainsString('1234567890', $line);
    }
}
