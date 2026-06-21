<?php

namespace Tests\Feature\Admin;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests that an admin with the super_admin role can actually use the Filament panel.
 */
class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_is_redirected_from_the_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    #[Test]
    public function a_user_with_no_role_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    #[Test]
    public function a_super_admin_can_access_the_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    #[Test]
    public function a_super_admin_can_access_the_car_list_and_edit_pages(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        $this->actingAs($user)->get('/admin/cars')->assertOk();
        $this->actingAs($user)->get("/admin/cars/{$car->uuid}/edit")->assertOk();
    }
}
