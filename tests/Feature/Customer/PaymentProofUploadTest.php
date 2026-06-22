<?php

namespace Tests\Feature\Customer;

use App\Enums\OrderStatus;
use App\Events\PaymentProofUploaded;
use App\Livewire\Customer\OrderDetail;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests uploading payment proof on an order (US-41).
 */
class PaymentProofUploadTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(array $attributes = []): Order
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        return Order::factory()->create(array_merge([
            'car_id' => $car->id,
            'status' => OrderStatus::PendingPayment,
        ], $attributes));
    }

    #[Test]
    public function a_customer_can_upload_a_payment_proof_with_a_note(): void
    {
        Storage::fake('private');
        Event::fake([PaymentProofUploaded::class]);

        $order = $this->makeOrder();
        $user = User::find($order->user_id);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->set('paymentProofFile', UploadedFile::fake()->image('receipt.jpg'))
            ->set('transactionNote', 'MTN MoMo ref 123456')
            ->call('uploadPaymentProof');

        $order->refresh();
        $this->assertSame(OrderStatus::PaymentUploaded, $order->status);
        $this->assertCount(1, $order->paymentProofs);
        $this->assertSame('MTN MoMo ref 123456', $order->paymentProofs->first()->note);
        Storage::disk('private')->assertExists($order->paymentProofs->first()->file_path);
        Event::assertDispatched(PaymentProofUploaded::class);
    }

    #[Test]
    public function multiple_proofs_can_be_uploaded_for_the_same_order(): void
    {
        Storage::fake('private');

        $order = $this->makeOrder();
        $user = User::find($order->user_id);

        $component = Livewire::actingAs($user)->test(OrderDetail::class, ['order' => $order]);

        $component->set('paymentProofFile', UploadedFile::fake()->image('first.jpg'))->call('uploadPaymentProof');
        $component->set('paymentProofFile', UploadedFile::fake()->image('second.jpg'))->call('uploadPaymentProof');

        $this->assertCount(2, $order->refresh()->paymentProofs);
    }

    #[Test]
    public function the_upload_option_is_hidden_once_payment_is_confirmed(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PaymentConfirmed]);
        $user = User::find($order->user_id);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->assertSet('canUploadProof', false);
    }

    #[Test]
    public function admin_is_notified_by_email_when_a_proof_is_uploaded(): void
    {
        Storage::fake('private');
        \Illuminate\Support\Facades\Notification::fake();
        Setting::set('contact_email', 'admin@livingstonautos.com');

        $order = $this->makeOrder();
        $user = User::find($order->user_id);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->set('paymentProofFile', UploadedFile::fake()->image('receipt.jpg'))
            ->call('uploadPaymentProof');

        \Illuminate\Support\Facades\Notification::assertSentOnDemand(
            \App\Notifications\PaymentProofUploadedNotification::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === Setting::get('contact_email');
            }
        );
    }

    #[Test]
    public function a_customer_cannot_upload_proof_to_another_customers_order(): void
    {
        $order = $this->makeOrder();
        $stranger = User::factory()->create();

        Livewire::actingAs($stranger)
            ->test(OrderDetail::class, ['order' => $order])
            ->assertForbidden();
    }
}
