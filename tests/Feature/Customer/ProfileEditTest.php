<?php

namespace Tests\Feature\Customer;

use App\Enums\KycStatus;
use App\Events\KycDocumentsSubmitted;
use App\Livewire\Customer\ProfileEdit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the customer profile & KYC document management page (US-43 / US-44).
 */
class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_view_the_profile_page(): void
    {
        $this->get(route('dashboard.profile'))->assertRedirect(route('login'));
    }

    #[Test]
    public function a_customer_can_update_their_name_and_address(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        Livewire::actingAs($user)
            ->test(ProfileEdit::class)
            ->set('name', 'New Name')
            ->set('address', '12 Liberation Road, Accra')
            ->set('ghana_card_number', 'GHA-000111222-3')
            ->call('updateProfile');

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('12 Liberation Road, Accra', $user->address);
    }

    #[Test]
    public function uploading_a_new_kyc_document_resets_status_to_pending_and_notifies_admin(): void
    {
        Storage::fake('private');
        Event::fake([KycDocumentsSubmitted::class]);

        $user = User::factory()->create([
            'kyc_status' => KycStatus::NeedsResubmission,
            'kyc_notes' => 'Photo was blurry.',
        ]);

        Livewire::actingAs($user)
            ->test(ProfileEdit::class)
            ->set('address', '1 Test Street')
            ->set('ghana_card_number', 'GHA-000111222-3')
            ->set('ghana_card_file', UploadedFile::fake()->image('card.jpg'))
            ->call('updateProfile');

        $user->refresh();
        $this->assertSame(KycStatus::Pending, $user->kyc_status);
        $this->assertNull($user->kyc_notes);
        Storage::disk('private')->assertExists($user->ghana_card_path);
        Event::assertDispatched(KycDocumentsSubmitted::class);
    }

    #[Test]
    public function replacing_a_document_deletes_the_old_file(): void
    {
        Storage::fake('private');

        $user = User::factory()->create();
        $oldPath = UploadedFile::fake()->image('old.jpg')->store("kyc/{$user->uuid}", 'private');
        $user->update(['ghana_card_path' => $oldPath]);

        Livewire::actingAs($user)
            ->test(ProfileEdit::class)
            ->set('address', '1 Test Street')
            ->set('ghana_card_number', 'GHA-000111222-3')
            ->set('ghana_card_file', UploadedFile::fake()->image('new.jpg'))
            ->call('updateProfile');

        Storage::disk('private')->assertMissing($oldPath);
        Storage::disk('private')->assertExists($user->refresh()->ghana_card_path);
    }

    #[Test]
    public function the_resubmission_reason_is_shown_when_kyc_needs_resubmission(): void
    {
        $user = User::factory()->create([
            'kyc_status' => KycStatus::NeedsResubmission,
            'kyc_notes' => 'Ghana Card photo was blurry, please re-upload.',
        ]);

        Livewire::actingAs($user)
            ->test(ProfileEdit::class)
            ->assertSee('Ghana Card photo was blurry, please re-upload.');
    }

    #[Test]
    public function document_uploaded_does_not_show_for_a_rejected_document_still_on_file(): void
    {
        // The old file is still on disk until the customer replaces it, but it
        // was the document that got rejected — so it shouldn't read as "uploaded".
        $user = User::factory()->create([
            'kyc_status' => KycStatus::NeedsResubmission,
            'kyc_notes' => 'Photo was blurry.',
            'ghana_card_path' => 'kyc/some-uuid/card.jpg',
        ]);

        Livewire::actingAs($user)
            ->test(ProfileEdit::class)
            ->assertDontSee('Document uploaded');
    }

    #[Test]
    public function a_customer_can_request_and_use_a_phone_verification_code(): void
    {
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => true], 200)]);

        $user = User::factory()->create(['phone' => '0551234567']);

        $component = Livewire::actingAs($user)
            ->test(ProfileEdit::class)
            ->call('sendPhoneVerificationCode');

        $code = $user->refresh()->phone_verification_code;
        $this->assertNotNull($code);
        $this->assertDatabaseHas('sms_logs', ['phone' => '0551234567', 'context' => 'otp']);

        $component->set('verificationCode', $code)
            ->call('verifyPhone');

        $this->assertNotNull($user->refresh()->phone_verified_at);
    }

    #[Test]
    public function the_customer_sees_an_error_when_the_sms_gateway_is_down(): void
    {
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => false], 500)]);

        $user = User::factory()->create(['phone' => '0551234567']);

        Livewire::actingAs($user)
            ->test(ProfileEdit::class)
            ->call('sendPhoneVerificationCode')
            ->assertHasErrors('phone');
    }

    #[Test]
    public function a_second_otp_send_within_a_minute_is_rate_limited(): void
    {
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => true], 200)]);

        $user = User::factory()->create(['phone' => '0551234567']);

        $component = Livewire::actingAs($user)->test(ProfileEdit::class);

        $component->call('sendPhoneVerificationCode');
        $firstCode = $user->refresh()->phone_verification_code;

        // Without the rate limit, this second call would overwrite the code
        // and send another real SMS — exactly the abuse vector being fixed.
        $component->call('sendPhoneVerificationCode')->assertHasErrors('phone');

        $this->assertSame($firstCode, $user->refresh()->phone_verification_code);
        Http::assertSentCount(1);
    }

    #[Test]
    public function an_expired_verification_code_is_rejected(): void
    {
        $user = User::factory()->create([
            'phone' => '0551234567',
            'phone_verification_code' => '123456',
            'phone_verification_code_expires_at' => now()->subMinute(),
        ]);

        Livewire::actingAs($user)
            ->test(ProfileEdit::class)
            ->set('verificationCode', '123456')
            ->call('verifyPhone')
            ->assertHasErrors('verificationCode');

        $this->assertNull($user->refresh()->phone_verified_at);
    }

    #[Test]
    public function repeated_wrong_codes_are_rate_limited(): void
    {
        $user = User::factory()->create([
            'phone' => '0551234567',
            'phone_verification_code' => '123456',
            'phone_verification_code_expires_at' => now()->addMinutes(10),
        ]);

        $component = Livewire::actingAs($user)->test(ProfileEdit::class);

        // 5 wrong guesses is the limit — the 6th should be blocked even if
        // the customer then guesses correctly, proving the lockout holds.
        for ($i = 0; $i < 5; $i++) {
            $component->set('verificationCode', '000000')->call('verifyPhone');
        }

        $component->set('verificationCode', '123456')
            ->call('verifyPhone')
            ->assertHasErrors('verificationCode');

        $this->assertNull($user->refresh()->phone_verified_at);
    }
}
