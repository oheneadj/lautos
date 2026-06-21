<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the default permission matrix from docs.md US-04 — staff_admin gets
 * order management out of the box but nothing else, while super_admin
 * bypasses every check.
 */
class RolesPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoles(): void
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    #[Test]
    public function staff_admin_can_access_order_management(): void
    {
        $this->seedRoles();

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole('staff_admin');

        $this->actingAs($user)->get('/admin/orders')->assertOk();
    }

    #[Test]
    public function staff_admin_cannot_access_roles_and_permissions(): void
    {
        $this->seedRoles();

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole('staff_admin');

        $this->actingAs($user)->get('/admin/shield/roles')->assertForbidden();
    }

    #[Test]
    public function staff_admin_cannot_manage_cars_by_default(): void
    {
        $this->seedRoles();

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole('staff_admin');

        $this->actingAs($user)->get('/admin/cars')->assertForbidden();
    }

    #[Test]
    public function super_admin_can_access_roles_and_permissions(): void
    {
        $this->seedRoles();

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($user)->get('/admin/shield/roles')->assertOk();
    }
}
