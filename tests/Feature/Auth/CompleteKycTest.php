<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\CompleteKyc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the KYC completion step customers land on after registering (US-36).
 */
class CompleteKycTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_customer_can_complete_kyc_with_a_ghana_card_only(): void
    {
        Storage::fake('private');
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CompleteKyc::class)
            ->set('phone', '0244000000')
            ->set('address', '123 Independence Ave, Accra')
            ->set('ghana_card_number', 'GHA-123456789-0')
            ->set('ghana_card_file', UploadedFile::fake()->image('card.jpg'))
            ->call('submit')
            ->assertRedirect(route('dashboard.index'));

        $user->refresh();

        $this->assertSame('123 Independence Ave, Accra', $user->address);
        $this->assertNotNull($user->ghana_card_path);
        Storage::disk('private')->assertExists($user->ghana_card_path);
    }

    #[Test]
    public function it_requires_at_least_a_ghana_card_number_or_a_tin(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CompleteKyc::class)
            ->set('phone', '0244000000')
            ->set('address', '123 Independence Ave, Accra')
            ->call('submit')
            ->assertHasErrors(['ghana_card_number', 'tin_number']);
    }

    #[Test]
    public function the_kyc_step_can_be_skipped(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CompleteKyc::class)
            ->call('skip')
            ->assertRedirect(route('dashboard.index'));

        $this->assertNull($user->refresh()->address);
    }

    #[Test]
    public function a_guest_cannot_access_the_kyc_step(): void
    {
        $this->get(route('register.kyc'))->assertRedirect(route('login'));
    }
}
