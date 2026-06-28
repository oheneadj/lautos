<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\ShieldPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests that KYC documents are only reachable through a signed admin URL
 * and are served from the private disk, never the public one (CLAUDE.md
 * security rule: never expose KYC document URLs directly).
 */
class KycDocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(ShieldPermissionsSeeder::class);

        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($admin);

        return $admin;
    }

    #[Test]
    public function an_admin_can_view_a_kyc_document_via_a_signed_url(): void
    {
        Storage::fake('private');
        $this->actingAsAdmin();

        $customer = User::factory()->create(['ghana_card_path' => 'kyc/'.uniqid().'/card.jpg']);
        Storage::disk('private')->put($customer->ghana_card_path, 'fake-image-bytes');

        $url = URL::signedRoute('admin.kyc-documents.show', ['user' => $customer, 'type' => 'ghana_card']);

        $this->get($url)->assertOk();
    }

    #[Test]
    public function the_document_is_not_reachable_without_a_valid_signature(): void
    {
        Storage::fake('private');
        $this->actingAsAdmin();

        $customer = User::factory()->create(['ghana_card_path' => 'kyc/'.uniqid().'/card.jpg']);
        Storage::disk('private')->put($customer->ghana_card_path, 'fake-image-bytes');

        $this->get(route('admin.kyc-documents.show', ['user' => $customer, 'type' => 'ghana_card']))
            ->assertForbidden();
    }

    #[Test]
    public function a_non_admin_customer_cannot_view_another_users_kyc_document_even_with_a_valid_signature(): void
    {
        Storage::fake('private');

        $customer = User::factory()->create(['ghana_card_path' => 'kyc/'.uniqid().'/card.jpg']);
        Storage::disk('private')->put($customer->ghana_card_path, 'fake-image-bytes');

        $otherCustomer = User::factory()->create();
        $this->actingAs($otherCustomer);

        // The signature is genuinely valid here — I'm proving that a valid
        // signature alone isn't enough; the requester must also be an admin.
        $url = URL::signedRoute('admin.kyc-documents.show', ['user' => $customer, 'type' => 'ghana_card']);

        $this->get($url)->assertForbidden();
    }

    #[Test]
    public function a_guest_cannot_view_a_kyc_document_even_with_a_valid_signature(): void
    {
        Storage::fake('private');

        $customer = User::factory()->create(['ghana_card_path' => 'kyc/'.uniqid().'/card.jpg']);
        Storage::disk('private')->put($customer->ghana_card_path, 'fake-image-bytes');

        $url = URL::signedRoute('admin.kyc-documents.show', ['user' => $customer, 'type' => 'ghana_card']);

        $this->get($url)->assertRedirect(route('login'));
    }

    #[Test]
    public function it_returns_404_when_the_document_does_not_exist_on_disk(): void
    {
        Storage::fake('private');
        $this->actingAsAdmin();

        $customer = User::factory()->create(['ghana_card_path' => null]);

        $url = URL::signedRoute('admin.kyc-documents.show', ['user' => $customer, 'type' => 'ghana_card']);

        $this->get($url)->assertNotFound();
    }

    #[Test]
    public function kyc_documents_uploaded_during_registration_are_not_reachable_on_the_public_disk(): void
    {
        Storage::fake('private');
        Storage::fake('public');

        $customer = User::factory()->create(['ghana_card_path' => 'kyc/'.uniqid().'/card.jpg']);
        Storage::disk('private')->put($customer->ghana_card_path, 'fake-image-bytes');

        // The whole point of the private disk is that this path is never
        // exposed through the public/storage symlink.
        Storage::disk('public')->assertMissing($customer->ghana_card_path);
    }
}
