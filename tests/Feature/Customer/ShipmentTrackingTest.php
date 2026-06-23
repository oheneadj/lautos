<?php

namespace Tests\Feature\Customer;

use App\Enums\OrderStatus;
use App\Livewire\Customer\OrderDetail;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the order detail shipment tracking timeline (US-42).
 */
class ShipmentTrackingTest extends TestCase
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
    public function it_shows_all_9_stages_in_sequence(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PaymentConfirmed]);
        $user = User::find($order->user_id);

        $component = Livewire::actingAs($user)->test(OrderDetail::class, ['order' => $order]);

        $pipeline = $component->get('pipeline');

        $this->assertCount(9, $pipeline);
        $this->assertSame(
            array_map(fn ($status) => $status->label(), OrderStatus::pipeline()),
            array_column($pipeline, 'label')
        );
    }

    #[Test]
    public function completed_current_and_future_stages_are_correctly_flagged(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::Purchased]);
        $user = User::find($order->user_id);

        $pipeline = Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->get('pipeline');

        $byValue = collect($pipeline)->keyBy('value');

        $this->assertTrue($byValue['pending_payment']['completed']);
        $this->assertTrue($byValue['purchased']['current']);
        $this->assertTrue($byValue['shipped']['future']);
    }

    #[Test]
    public function the_demurrage_warning_shows_from_arrived_in_ghana_onwards(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PaymentConfirmed]);
        $user = User::find($order->user_id);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->assertSet('showDemurrageWarning', false);

        $order->update(['status' => OrderStatus::ArrivedInGhana]);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->assertSet('showDemurrageWarning', true);
    }

    #[Test]
    public function the_estimated_arrival_date_is_shown_once_shipped(): void
    {
        $order = $this->makeOrder([
            'status' => OrderStatus::Shipped,
            'estimated_arrival_date' => now()->addWeeks(2),
        ]);
        $user = User::find($order->user_id);

        $this->actingAs($user)
            ->get(route('dashboard.orders.show', $order->uuid))
            ->assertSee($order->estimated_arrival_date->format('d F, Y'));
    }

    #[Test]
    public function refreshing_the_order_picks_up_a_status_change_made_elsewhere(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PendingPayment]);
        $user = User::find($order->user_id);

        $component = Livewire::actingAs($user)->test(OrderDetail::class, ['order' => $order]);

        // Simulate an admin advancing the order while this page is open.
        Order::where('id', $order->id)->update(['status' => OrderStatus::PaymentUploaded]);

        $component->call('refreshOrder');

        $this->assertSame(OrderStatus::PaymentUploaded, $component->get('order')->status);
    }

    #[Test]
    public function a_rejected_payment_shows_the_admins_reason_on_the_dashboard(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);
        $user = User::find($order->user_id);

        (new \App\Services\OrderService())->rejectPayment($order, 'Amount does not match the invoice.');

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order->refresh()])
            ->assertSee('Payment Proof Rejected')
            ->assertSee('Amount does not match the invoice.');
    }

    #[Test]
    public function a_brand_new_order_does_not_show_a_rejection_notice(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PendingPayment]);
        $user = User::find($order->user_id);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->assertDontSee('Payment Proof Rejected');
    }

    #[Test]
    public function a_cancelled_order_shows_the_cancellation_reason_instead_of_the_timeline(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PendingPayment]);
        $user = User::find($order->user_id);

        $order->statusHistories()->create([
            'status' => OrderStatus::Cancelled,
            'notes' => 'Another buyer completed payment for this car first.',
        ]);
        $order->update(['status' => OrderStatus::Cancelled]);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order->refresh()])
            ->assertSee('Order Cancelled')
            ->assertSee('Another buyer completed payment for this car first.')
            ->assertDontSee('Shipment Timeline');
    }

    #[Test]
    public function an_active_order_does_not_show_a_cancellation_notice(): void
    {
        $order = $this->makeOrder(['status' => OrderStatus::PendingPayment]);
        $user = User::find($order->user_id);

        Livewire::actingAs($user)
            ->test(OrderDetail::class, ['order' => $order])
            ->assertDontSee('Order Cancelled')
            ->assertSee('Shipment Timeline');
    }
}
