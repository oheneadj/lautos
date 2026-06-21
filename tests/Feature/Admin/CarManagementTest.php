<?php

namespace Tests\Feature\Admin;

use App\Enums\CarStatus;
use App\Filament\Resources\Cars\Pages\ListCars;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin car listing table (US-09) — search, filters, sort,
 * bulk status changes, and the Archived tab.
 */
class CarManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(array $attributes = []): Car
    {
        $make = Make::firstOrCreate(['name' => $attributes['make'] ?? 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => $attributes['model'] ?? 'Corolla']);

        return Car::factory()->create(array_merge([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
        ], array_diff_key($attributes, array_flip(['make', 'model']))));
    }

    private function actingAsAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function it_searches_by_make_model_and_colour(): void
    {
        $this->actingAsAdmin();

        $match = $this->makeCar(['colour' => 'Pearl White']);
        $other = $this->makeCar(['make' => 'Honda', 'model' => 'Civic', 'colour' => 'Black']);

        Livewire::test(ListCars::class)
            ->searchTable('Pearl White')
            ->assertCanSeeTableRecords([$match])
            ->assertCanNotSeeTableRecords([$other]);
    }

    #[Test]
    public function it_filters_by_status_make_fuel_type_transmission_and_country(): void
    {
        $this->actingAsAdmin();

        $match = $this->makeCar([
            'status' => CarStatus::Available,
            'fuel_type' => 'Petrol',
            'transmission' => 'Manual',
            'country_of_origin' => 'Japan',
        ]);
        $other = $this->makeCar([
            'status' => CarStatus::Sold,
            'fuel_type' => 'Diesel',
            'transmission' => 'Automatic',
            'country_of_origin' => 'Korea',
        ]);

        Livewire::test(ListCars::class)
            ->filterTable('status', CarStatus::Available->value)
            ->assertCanSeeTableRecords([$match])
            ->assertCanNotSeeTableRecords([$other]);
    }

    #[Test]
    public function bulk_change_status_updates_every_selected_car(): void
    {
        $this->actingAsAdmin();

        $car = $this->makeCar(['status' => CarStatus::Available]);

        Livewire::test(ListCars::class)
            ->callTableBulkAction('changeStatus', [$car], data: ['status' => CarStatus::Reserved->value]);

        $this->assertSame(CarStatus::Reserved, $car->refresh()->status);
    }

    #[Test]
    public function archived_tab_shows_soft_deleted_cars_and_the_default_tab_excludes_them(): void
    {
        $this->actingAsAdmin();

        $active = $this->makeCar();
        $archived = $this->makeCar(['make' => 'Honda', 'model' => 'Civic']);
        $archived->delete();

        Livewire::test(ListCars::class)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$archived])
            ->set('activeTab', 'archived')
            ->assertCanSeeTableRecords([$archived])
            ->assertCanNotSeeTableRecords([$active]);
    }
}
