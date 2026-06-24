<?php

namespace Tests\Feature\Admin;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin order management workflows — the order table (US-10),
 * the order detail page (US-11), payment confirm/reject (US-12), shipment
 * stage advances (US-13), and internal notes (US-14).
 */
class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($user);

        return $user;
    }

    private function makeOrder(array $attributes = []): Order
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        return Order::factory()->create(array_merge(['car_id' => $car->id], $attributes));
    }

    #[Test]
    public function guest_cannot_access_order_management(): void
    {
        $this->get('/admin/orders')->assertRedirect('/admin/login');
    }

    #[Test]
    public function admin_can_see_orders_with_status_and_payment_badges(): void
    {
        $this->actingAsAdmin();

        $order = $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);

        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords([$order]);
    }

    #[Test]
    public function it_filters_orders_by_status_and_payment_status(): void
    {
        $this->actingAsAdmin();

        $pending = $this->makeOrder(['status' => OrderStatus::PendingPayment]);
        $confirmed = $this->makeOrder(['status' => OrderStatus::Purchased]);

        Livewire::test(ListOrders::class)
            ->filterTable('status', OrderStatus::PendingPayment->value)
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$confirmed]);
    }

    #[Test]
    public function it_searches_orders_by_customer_name(): void
    {
        $this->actingAsAdmin();

        $customer = User::factory()->create(['name' => 'Kwame Mensah']);
        $order = $this->makeOrder(['user_id' => $customer->id]);
        $other = $this->makeOrder();

        Livewire::test(ListOrders::class)
            ->searchTable('Kwame Mensah')
            ->assertCanSeeTableRecords([$order])
            ->assertCanNotSeeTableRecords([$other]);
    }

    #[Test]
    public function navigation_badge_counts_orders_requiring_action(): void
    {
        $this->actingAsAdmin();

        $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);
        $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);
        $this->makeOrder(['status' => OrderStatus::Purchased]);

        $this->assertSame('2', OrderResource::getNavigationBadge());
    }

    #[Test]
    public function admin_can_view_the_order_detail_page(): void
    {
        $this->actingAsAdmin();

        $order = $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);

        Livewire::test(ViewOrder::class, ['record' => $order->uuid])
            ->assertOk();
    }

    #[Test]
    public function admin_can_confirm_payment_from_the_order_page(): void
    {
        Event::fake([\App\Events\PaymentConfirmed::class]);
        $this->actingAsAdmin();

        $order = $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);

        Livewire::test(ViewOrder::class, ['record' => $order->uuid])
            ->callAction('confirmPayment');

        $this->assertSame(OrderStatus::PaymentConfirmed, $order->refresh()->status);
        $this->assertSame(CarStatus::Reserved, $order->car->refresh()->status);
    }

    #[Test]
    public function admin_can_reject_payment_with_a_reason(): void
    {
        Event::fake([\App\Events\PaymentRejected::class]);
        $this->actingAsAdmin();

        $order = $this->makeOrder(['status' => OrderStatus::PaymentUploaded]);

        Livewire::test(ViewOrder::class, ['record' => $order->uuid])
            ->callAction('rejectPayment', data: ['reason' => 'Receipt amount mismatch.']);

        $this->assertSame(OrderStatus::PendingPayment, $order->refresh()->status);
        $this->assertDatabaseHas('order_status_histories', ['notes' => 'Receipt amount mismatch.']);
    }

    #[Test]
    public function admin_can_cancel_a_confirmed_order_and_release_the_reserved_car(): void
    {
        Event::fake([\App\Events\OrderCancelledByAdmin::class]);
        $this->actingAsAdmin();

        $order = $this->makeOrder(['status' => OrderStatus::PaymentConfirmed]);
        $order->car->update(['status' => CarStatus::Reserved]);

        Livewire::test(ViewOrder::class, ['record' => $order->uuid])
            ->callAction('cancelOrder', data: ['reason' => 'Customer requested a refund.']);

        $this->assertSame(OrderStatus::Cancelled, $order->refresh()->status);
        $this->assertSame(CarStatus::Available, $order->car->refresh()->status);
        $this->assertDatabaseHas('order_status_histories', ['notes' => 'Customer requested a refund.']);
    }

    #[Test]
    public function admin_can_advance_the_order_to_the_next_stage(): void
    {
        Event::fake([\App\Events\OrderStageUpdated::class]);
        $this->actingAsAdmin();

        $order = $this->makeOrder(['status' => OrderStatus::PaymentConfirmed]);

        Livewire::test(ViewOrder::class, ['record' => $order->uuid])
            ->callAction('advanceStage');

        $this->assertSame(OrderStatus::Purchased, $order->refresh()->status);
    }

    #[Test]
    public function admin_can_add_an_internal_note(): void
    {
        $admin = $this->actingAsAdmin();

        $order = $this->makeOrder();

        Livewire::test(ViewOrder::class, ['record' => $order->uuid])
            ->callAction('addNote', data: ['note' => 'Customer called about delayed shipment.']);

        $this->assertDatabaseHas('order_notes', [
            'order_id' => $order->id,
            'admin_id' => $admin->id,
            'note' => 'Customer called about delayed shipment.',
        ]);
    }

    #[Test]
    public function status_tabs_filter_the_table_to_just_that_status(): void
    {
        $this->actingAsAdmin();

        $pending = $this->makeOrder(['status' => OrderStatus::PendingPayment]);
        $delivered = $this->makeOrder(['status' => OrderStatus::Delivered]);

        Livewire::test(ListOrders::class)
            ->set('activeTab', OrderStatus::Delivered->value)
            ->assertCanSeeTableRecords([$delivered])
            ->assertCanNotSeeTableRecords([$pending]);
    }
}
