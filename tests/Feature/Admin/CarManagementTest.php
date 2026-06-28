<?php

namespace Tests\Feature\Admin;

use App\Enums\CarBodyType;
use App\Enums\CarStatus;
use App\Filament\Resources\Cars\Pages\CreateCar;
use App\Filament\Resources\Cars\Pages\ListCars;
use App\Filament\Resources\Cars\Pages\ViewCar;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\User;
use Database\Seeders\ShieldPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
        $this->seed(ShieldPermissionsSeeder::class);

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
    public function it_filters_by_body_type(): void
    {
        $this->actingAsAdmin();

        $suv = $this->makeCar(['body_type' => CarBodyType::Suv]);
        $sedan = $this->makeCar(['body_type' => CarBodyType::Sedan]);

        Livewire::test(ListCars::class)
            ->filterTable('body_type', CarBodyType::Suv->value)
            ->assertCanSeeTableRecords([$suv])
            ->assertCanNotSeeTableRecords([$sedan]);
    }

    #[Test]
    public function admin_can_view_the_car_detail_page(): void
    {
        $this->actingAsAdmin();

        $car = $this->makeCar(['body_type' => CarBodyType::Suv]);

        Livewire::test(ViewCar::class, ['record' => $car->uuid])
            ->assertOk()
            ->assertSee('SUV');
    }

    #[Test]
    public function admin_can_set_the_body_type_when_creating_a_car(): void
    {
        $this->actingAsAdmin();

        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        Livewire::test(CreateCar::class)
            ->fillForm([
                'make_id' => $make->id,
                'car_model_id' => $carModel->id,
                'year' => 2022,
                'engine_capacity' => '1800cc',
                'transmission' => 'Automatic',
                'fuel_type' => 'Petrol',
                'mileage' => 30000,
                'colour' => 'White',
                'country_of_origin' => 'Japan',
                'body_type' => CarBodyType::Sedan->value,
                'image_paths' => [
                    UploadedFile::fake()->image('a.jpg'),
                    UploadedFile::fake()->image('b.jpg'),
                    UploadedFile::fake()->image('c.jpg'),
                ],
                'price_usd_cents' => 150,
                'shipping_cost_usd_cents' => 20,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('cars', [
            'make_id' => $make->id,
            'body_type' => CarBodyType::Sedan->value,
        ]);
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
    public function a_role_without_update_car_permission_cannot_change_status(): void
    {
        $this->seed(ShieldPermissionsSeeder::class);

        $viewer = User::factory()->create(['is_admin' => true]);
        $viewer->assignRole(Role::findOrCreate('car_viewer', 'web'));
        $viewer->syncPermissions(['ViewAny:Car', 'View:Car']);
        $this->actingAs($viewer);

        $car = $this->makeCar(['status' => CarStatus::Available]);

        // The bulk version uses authorizeIndividualRecords(), so it stays
        // visible but must silently skip records the user can't update.
        Livewire::test(ListCars::class)
            ->assertTableActionHidden('changeStatus', $car)
            ->callTableBulkAction('changeStatus', [$car], data: ['status' => CarStatus::Reserved->value]);

        $this->assertSame(CarStatus::Available, $car->refresh()->status);
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

    #[Test]
    public function status_tabs_filter_the_table_to_just_that_status(): void
    {
        $this->actingAsAdmin();

        $available = $this->makeCar(['status' => CarStatus::Available]);
        $sold = $this->makeCar(['make' => 'Honda', 'model' => 'Civic', 'status' => CarStatus::Sold]);

        Livewire::test(ListCars::class)
            ->set('activeTab', CarStatus::Sold->value)
            ->assertCanSeeTableRecords([$sold])
            ->assertCanNotSeeTableRecords([$available]);
    }
}
