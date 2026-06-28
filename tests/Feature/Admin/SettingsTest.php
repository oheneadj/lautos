<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Settings;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\ShieldPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    public function staff_admin_cannot_see_the_real_bank_and_momo_numbers(): void
    {
        // disabled() only blocks editing in the browser — the field still
        // renders whatever value is in form state, so this confirms the real
        // numbers never reach a staff_admin's page in the first place.
        $this->seedRoles();
        Setting::set('bank_account_number', 'real-account-number');
        Setting::set('momo_number', 'real-momo-number');

        $staff = User::factory()->create(['is_admin' => true]);
        $staff->assignRole(Role::findOrCreate('staff_admin', 'web'));
        $this->actingAs($staff);

        Livewire::test(Settings::class)
            ->assertFormSet(['bank_account_number' => null, 'momo_number' => null]);
    }

    #[Test]
    public function staff_admin_without_the_permission_does_not_see_the_real_exchange_rate(): void
    {
        $this->seedRoles();
        Setting::set('exchange_rate_usd_to_ghs', '15.50');

        $staff = User::factory()->create(['is_admin' => true]);
        $staff->assignRole(Role::findOrCreate('staff_admin', 'web'));
        $this->actingAs($staff);

        Livewire::test(Settings::class)
            ->assertFormSet(['exchange_rate_usd_to_ghs' => null]);
    }

    #[Test]
    public function staff_admin_cannot_persist_payment_details_even_by_bypassing_the_disabled_field(): void
    {
        // fillForm() sets the underlying state directly, the same way a
        // tampered Livewire request would bypass the disabled() UI hint —
        // this proves the backend re-check actually holds, not just the form.
        $this->seedRoles();
        $staff = User::factory()->create(['is_admin' => true]);
        $staff->assignRole(Role::findOrCreate('staff_admin', 'web'));
        $this->actingAs($staff);

        Setting::set('bank_account_number', 'original-account');

        Livewire::test(Settings::class)
            ->fillForm(['bank_account_number' => 'attacker-account'])
            ->call('save');

        $this->assertSame('original-account', Setting::get('bank_account_number'));
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

        Storage::fake('public');
        $logo = UploadedFile::fake()->image('logo.png');

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
