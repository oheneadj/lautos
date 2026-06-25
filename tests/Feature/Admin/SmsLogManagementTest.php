<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\SmsLogs\Pages\ListSmsLogs;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the read-only admin SMS log list — the troubleshooting view for
 * every GiantSMS request/response GiantSmsService records.
 */
class SmsLogManagementTest extends TestCase
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

    #[Test]
    public function guest_cannot_access_the_sms_logs_page(): void
    {
        $this->get(ListSmsLogs::getUrl())->assertRedirect('/admin/login');
    }

    #[Test]
    public function admin_can_see_sms_logs_in_the_list(): void
    {
        $this->actingAsAdmin();

        $log = SmsLog::factory()->create(['phone' => '0551234567']);

        Livewire::test(ListSmsLogs::class)->assertCanSeeTableRecords([$log]);
    }
}
