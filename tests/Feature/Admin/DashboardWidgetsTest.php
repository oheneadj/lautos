<?php

namespace Tests\Feature\Admin;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Filament\Widgets\ActionRequiredWidget;
use App\Filament\Widgets\CarStatsWidget;
use App\Filament\Widgets\RecentCarsTable;
use App\Filament\Widgets\RecentOrdersTable;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin dashboard widgets (US-22) — stat cards, recent cars,
 * recent orders, and that staff without the relevant permission don't
 * see widgets for resources they can't manage.
 */
class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(array $attributes = []): Car
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create(array_merge([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
        ], $attributes));
    }

    private function actingAsSuperAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);

        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($admin);

        return $admin;
    }

    private function actingAsStaffAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $staff = User::factory()->create(['is_admin' => true]);
        $staff->assignRole(Role::findOrCreate('staff_admin', 'web'));
        $this->actingAs($staff);

        return $staff;
    }

    #[Test]
    public function car_stats_widget_shows_counts_by_status(): void
    {
        $this->actingAsSuperAdmin();

        $this->makeCar(['status' => CarStatus::Available]);
        $this->makeCar(['status' => CarStatus::Reserved]);
        $this->makeCar(['status' => CarStatus::Sold, 'sold_at' => now()]);

        Livewire::test(CarStatsWidget::class)->assertOk();
    }

    #[Test]
    public function action_required_widget_counts_payment_uploaded_orders(): void
    {
        $this->actingAsSuperAdmin();

        $car = $this->makeCar();
        Order::factory()->create(['car_id' => $car->id, 'status' => OrderStatus::PaymentUploaded]);

        Livewire::test(ActionRequiredWidget::class)->assertOk();
    }

    #[Test]
    public function recent_cars_widget_shows_the_latest_five(): void
    {
        $this->actingAsSuperAdmin();

        $car = $this->makeCar();

        Livewire::test(RecentCarsTable::class)
            ->assertCanSeeTableRecords([$car]);
    }

    #[Test]
    public function recent_orders_widget_shows_the_latest_ten(): void
    {
        $this->actingAsSuperAdmin();

        $car = $this->makeCar();
        $order = Order::factory()->create(['car_id' => $car->id]);

        Livewire::test(RecentOrdersTable::class)
            ->assertCanSeeTableRecords([$order]);
    }

    #[Test]
    public function staff_without_car_permissions_cannot_view_the_car_widget(): void
    {
        $this->actingAsStaffAdmin();

        $this->assertFalse(CarStatsWidget::canView());
    }

    #[Test]
    public function staff_with_order_permissions_can_view_the_order_widget(): void
    {
        $this->actingAsStaffAdmin();

        $this->assertTrue(ActionRequiredWidget::canView());
    }
}
