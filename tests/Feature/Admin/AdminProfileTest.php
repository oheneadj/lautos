<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ProfilePhoneInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the Filament Breezy "My Profile" page for admin/staff users —
 * personal info, password, 2FA, and our custom phone-verification component.
 */
class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));

        return $admin;
    }

    #[Test]
    public function guest_cannot_access_the_admin_profile_page(): void
    {
        $this->get('/admin/my-profile')->assertRedirect('/admin/login');
    }

    #[Test]
    public function an_admin_can_view_their_profile_page(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get('/admin/my-profile')
            ->assertOk()
            ->assertSee('Phone Number');
    }

    #[Test]
    public function an_admin_can_save_their_phone_number(): void
    {
        $admin = $this->makeAdmin();

        // I hit the real page first so Filament's current-panel/auth-guard
        // context is actually set up — the Livewire sub-component relies on
        // Filament::getCurrentOrDefaultPanel(), which is only resolved by the
        // panel's own routing/middleware, not by Livewire::actingAs() alone.
        $this->actingAs($admin)->get('/admin/my-profile');

        Livewire::test(ProfilePhoneInfo::class)
            ->set('data.phone', '+233 55 123 4567')
            ->call('submit');

        // I assert the stripped form here — CLAUDE.md's Ghana phone pattern
        // requires spaces stripped before validating/saving.
        $this->assertSame('+233551234567', $admin->refresh()->phone);
        $this->assertNull($admin->phone_verified_at);
    }

    #[Test]
    public function an_admin_can_verify_their_phone_number_with_the_correct_code(): void
    {
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => true], 200)]);

        $admin = $this->makeAdmin();
        $admin->update(['phone' => '+233 55 123 4567']);

        $this->actingAs($admin)->get('/admin/my-profile');

        $component = Livewire::test(ProfilePhoneInfo::class)
            ->call('sendPhoneVerificationCode');

        $code = $admin->refresh()->phone_verification_code;

        $component->set('verificationCode', $code)
            ->call('verifyPhone');

        $this->assertNotNull($admin->refresh()->phone_verified_at);
        $this->assertNull($admin->phone_verification_code);
    }

    #[Test]
    public function an_admin_sees_an_error_for_the_wrong_verification_code(): void
    {
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => true], 200)]);

        $admin = $this->makeAdmin();
        $admin->update(['phone' => '+233 55 123 4567']);

        $this->actingAs($admin)->get('/admin/my-profile');

        Livewire::test(ProfilePhoneInfo::class)
            ->call('sendPhoneVerificationCode')
            ->set('verificationCode', '000000')
            ->call('verifyPhone')
            ->assertHasErrors('verificationCode');

        $this->assertNull($admin->refresh()->phone_verified_at);
    }

    #[Test]
    public function a_second_admin_otp_send_within_a_minute_is_rate_limited(): void
    {
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => true], 200)]);

        $admin = $this->makeAdmin();
        $admin->update(['phone' => '+233 55 123 4567']);

        $this->actingAs($admin)->get('/admin/my-profile');

        $component = Livewire::test(ProfilePhoneInfo::class);
        $component->call('sendPhoneVerificationCode');
        $firstCode = $admin->refresh()->phone_verification_code;

        $component->call('sendPhoneVerificationCode')->assertHasErrors('data.phone');

        $this->assertSame($firstCode, $admin->refresh()->phone_verification_code);
        Http::assertSentCount(1);
    }

    #[Test]
    public function an_expired_admin_verification_code_is_rejected(): void
    {
        $admin = $this->makeAdmin();
        $admin->update([
            'phone' => '+233 55 123 4567',
            'phone_verification_code' => '123456',
            'phone_verification_code_expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($admin)->get('/admin/my-profile');

        Livewire::test(ProfilePhoneInfo::class)
            ->set('verificationCode', '123456')
            ->call('verifyPhone')
            ->assertHasErrors('verificationCode');

        $this->assertNull($admin->refresh()->phone_verified_at);
    }

    #[Test]
    public function repeated_wrong_admin_codes_are_rate_limited(): void
    {
        $admin = $this->makeAdmin();
        $admin->update([
            'phone' => '+233 55 123 4567',
            'phone_verification_code' => '123456',
            'phone_verification_code_expires_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($admin)->get('/admin/my-profile');

        $component = Livewire::test(ProfilePhoneInfo::class);

        for ($i = 0; $i < 5; $i++) {
            $component->set('verificationCode', '000000')->call('verifyPhone');
        }

        $component->set('verificationCode', '123456')
            ->call('verifyPhone')
            ->assertHasErrors('verificationCode');

        $this->assertNull($admin->refresh()->phone_verified_at);
    }
}
