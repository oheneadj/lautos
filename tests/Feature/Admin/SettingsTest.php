<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Settings;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\ShieldPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin Settings page — exchange rate, payment details, and the
 * demurrage warning message (US-19/20/21).
 */
class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoles(): void
    {
        $this->seed(ShieldPermissionsSeeder::class);
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    #[Test]
    public function guest_cannot_access_settings(): void
    {
        $this->get('/admin/settings')->assertRedirect('/admin/login');
    }

    #[Test]
    public function super_admin_can_update_the_exchange_rate(): void
    {
        $this->seedRoles();
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($admin);

        Livewire::test(Settings::class)
            ->fillForm(['exchange_rate_usd_to_ghs' => '17.5'])
            ->call('save');

        $this->assertSame('17.5', Setting::get('exchange_rate_usd_to_ghs'));
    }

    #[Test]
    public function updating_a_setting_is_logged_to_the_activity_log(): void
    {
        $this->seedRoles();
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($admin);

        Livewire::test(Settings::class)
            ->fillForm(['bank_name' => 'GCB Bank', 'exchange_rate_usd_to_ghs' => '15'])
            ->call('save');

        $this->assertTrue(
            Activity::where('description', 'Updated setting: bank_name')
                ->where('causer_id', $admin->id)
                ->exists()
        );
    }

    #[Test]
    public function staff_admin_without_the_permission_cannot_edit_the_exchange_rate_field(): void
    {
        $this->seedRoles();
        $staff = User::factory()->create(['is_admin' => true]);
        $staff->assignRole(Role::findOrCreate('staff_admin', 'web'));
        $this->actingAs($staff);

        Livewire::test(Settings::class)
            ->assertFormFieldIsDisabled('exchange_rate_usd_to_ghs');
    }

    #[Test]
    public function admin_can_update_business_info(): void
    {
        $this->seedRoles();
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($admin);

        Livewire::test(Settings::class)
            ->fillForm([
                'site_name' => 'New Business Name',
                'contact_email' => 'hello@newbusiness.com',
                'contact_phone' => '+233 50 123 4567',
                'whatsapp_number' => '+233 50 123 4567',
                'contact_address' => 'Tema, Ghana',
                'facebook_url' => 'https://facebook.com/newbusiness',
                'instagram_url' => 'https://instagram.com/newbusiness',
                'twitter_url' => 'https://x.com/newbusiness',
                'exchange_rate_usd_to_ghs' => '15',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('New Business Name', Setting::get('site_name'));
        $this->assertSame('hello@newbusiness.com', Setting::get('contact_email'));
        $this->assertSame('https://facebook.com/newbusiness', Setting::get('facebook_url'));
        $this->assertSame('https://instagram.com/newbusiness', Setting::get('instagram_url'));
        $this->assertSame('https://x.com/newbusiness', Setting::get('twitter_url'));
    }

    #[Test]
    public function admin_can_upload_a_site_logo(): void
    {
        $this->seedRoles();
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($admin);

        \Illuminate\Support\Facades\Storage::fake('public');
        $logo = \Illuminate\Http\UploadedFile::fake()->image('logo.png');

        Livewire::test(Settings::class)
            ->fillForm([
                'site_logo_path' => $logo,
                'exchange_rate_usd_to_ghs' => '15',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotNull(Setting::get('site_logo_path'));
    }
}
