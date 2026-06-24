<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests that the admin (staff) user list stays separate from customers,
 * mirroring CustomerManagementTest's split in the other direction.
 */
class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);

        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($admin);

        return $admin;
    }

    #[Test]
    public function guest_cannot_access_admin_user_management(): void
    {
        $this->get('/admin/users')->assertRedirect('/admin/login');
    }

    #[Test]
    public function the_admin_list_excludes_customer_accounts(): void
    {
        $admin = $this->actingAsAdmin();

        $otherAdmin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['is_admin' => false]);

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords([$admin, $otherAdmin])
            ->assertCanNotSeeTableRecords([$customer]);
    }

    #[Test]
    public function admin_can_create_a_new_admin_user(): void
    {
        $this->actingAsAdmin();

        $role = Role::findOrCreate('support_staff', 'web');

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New Staff Member',
                'email' => 'staff@livingstonautos.com',
                'password' => 'password123',
                'is_admin' => true,
                'roles' => [$role->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $newUser = User::where('email', 'staff@livingstonautos.com')->first();

        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->is_admin);
        $this->assertTrue($newUser->hasRole('support_staff'));
    }
}
