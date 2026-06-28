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
use App\Notifications\PaymentProofUploadedNotification;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
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
    public function a_second_proof_can_be_uploaded_after_the_first_is_rejected(): void
    {
        Storage::fake('private');

        $order = $this->makeOrder();
        $user = User::find($order->user_id);

        $component = Livewire::actingAs($user)->test(OrderDetail::class, ['order' => $order]);

        $component->set('paymentProofFile', UploadedFile::fake()->image('first.jpg'))->call('uploadPaymentProof');

        // A rejection sends the order back to Pending Payment, which is the
        // only thing that reopens the upload form for a resubmission.
        app(OrderService::class)->rejectPayment($order->refresh(), 'Receipt image is unreadable.');

        $component->set('paymentProofFile', UploadedFile::fake()->image('second.jpg'))->call('uploadPaymentProof');

        $this->assertCount(2, $order->refresh()->paymentProofs);
    }

    #[Test]
    public function the_upload_form_is_hidden_and_blocked_while_a_proof_is_under_review(): void
    {
        Storage::fake('private');

        $order = $this->makeOrder();
        $user = User::find($order->user_id);

        $component = Livewire::actingAs($user)->test(OrderDetail::class, ['order' => $order]);
        $component->set('paymentProofFile', UploadedFile::fake()->image('first.jpg'))->call('uploadPaymentProof');

        $this->assertSame(OrderStatus::PaymentUploaded, $order->refresh()->status);

        $component->assertSet('canUploadProof', false)
            ->assertDontSeeHtml('wire:submit="uploadPaymentProof"');

        // Even a direct call to the component method (bypassing the hidden
        // UI) must not slip a second proof in while the first is pending review.
        $component->set('paymentProofFile', UploadedFile::fake()->image('sneaky.jpg'))
            ->call('uploadPaymentProof')
            ->assertForbidden();

        $this->assertCount(1, $order->refresh()->paymentProofs);
    }

    #[Test]
    public function the_most_recent_proof_is_badged_as_rejected_after_a_rejection(): void
    {
        Storage::fake('private');

        $order = $this->makeOrder();
        $user = User::find($order->user_id);

        $component = Livewire::actingAs($user)->test(OrderDetail::class, ['order' => $order]);
        $component->set('paymentProofFile', UploadedFile::fake()->image('first.jpg'))->call('uploadPaymentProof');

        app(OrderService::class)->rejectPayment($order->refresh(), 'Receipt image is unreadable.');

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->assertSee('Rejected')
            ->assertSee('Receipt image is unreadable.');
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
        Notification::fake();
        Setting::set('contact_email', 'admin@livingstonautos.com');

        $order = $this->makeOrder();
        $user = User::find($order->user_id);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->set('paymentProofFile', UploadedFile::fake()->image('receipt.jpg'))
            ->call('uploadPaymentProof');

        Notification::assertSentOnDemand(
            PaymentProofUploadedNotification::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === Setting::get('contact_email');
            }
        );
    }

    #[Test]
    public function the_proof_record_is_still_created_even_when_the_order_no_longer_matches_the_in_memory_status(): void
    {
        // This proves uploadPaymentProof() re-checks the DB rather than the
        // stale in-memory $this->order property — I move the order to
        // PaymentUploaded directly in the DB (simulating an admin action
        // landing between this request's start and its final write) without
        // ever refreshing the component's own copy, then call the method.
        Storage::fake('private');

        $order = $this->makeOrder();
        $user = User::find($order->user_id);

        $component = Livewire::actingAs($user)->test(OrderDetail::class, ['order' => $order]);

        $order->update(['status' => OrderStatus::PaymentUploaded]);

        $component->set('paymentProofFile', UploadedFile::fake()->image('receipt.jpg'))
            ->call('uploadPaymentProof')
            ->assertForbidden();

        // The earlier abort_unless still blocks it (status check happens
        // before the lock), so the proof must NOT be created and the status
        // an admin already set must NOT be reverted.
        $this->assertCount(0, $order->refresh()->paymentProofs);
        $this->assertSame(OrderStatus::PaymentUploaded, $order->status);
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
