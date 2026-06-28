<?php

namespace Tests\Feature\Customer;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use Database\Seeders\ShieldPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests that support ticket attachments are only reachable through a signed
 * URL, served from the private disk, and viewable by either the ticket's own
 * customer or an admin — never a different customer.
 */
class TicketAttachmentControllerTest extends TestCase
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

    private function makeMessage(User $customer, string $path): TicketMessage
    {
        $ticket = SupportTicket::create([
            'user_id' => $customer->id,
            'subject' => 'I need help',
            'status' => 'Open',
        ]);

        return $ticket->messages()->create([
            'user_id' => $customer->id,
            'message' => 'Here is a screenshot.',
            'is_admin' => false,
            'attachment_path' => $path,
        ]);
    }

    #[Test]
    public function the_ticket_owner_can_view_their_own_attachment_via_a_signed_url(): void
    {
        Storage::fake('private');

        $customer = User::factory()->create();
        $message = $this->makeMessage($customer, 'tickets/attachments/'.uniqid().'/screenshot.jpg');
        Storage::disk('private')->put($message->attachment_path, 'fake-image-bytes');

        $this->actingAs($customer);

        $url = URL::signedRoute('ticket-attachments.show', ['message' => $message]);

        $this->get($url)->assertOk();
    }

    #[Test]
    public function an_admin_can_view_any_customers_attachment_via_a_signed_url(): void
    {
        Storage::fake('private');

        $customer = User::factory()->create();
        $message = $this->makeMessage($customer, 'tickets/attachments/'.uniqid().'/screenshot.jpg');
        Storage::disk('private')->put($message->attachment_path, 'fake-image-bytes');

        $this->actingAsAdmin();

        $url = URL::signedRoute('ticket-attachments.show', ['message' => $message]);

        $this->get($url)->assertOk();
    }

    #[Test]
    public function a_different_customer_cannot_view_someone_elses_attachment_even_with_a_valid_signature(): void
    {
        Storage::fake('private');

        $owner = User::factory()->create();
        $message = $this->makeMessage($owner, 'tickets/attachments/'.uniqid().'/screenshot.jpg');
        Storage::disk('private')->put($message->attachment_path, 'fake-image-bytes');

        $intruder = User::factory()->create();
        $this->actingAs($intruder);

        $url = URL::signedRoute('ticket-attachments.show', ['message' => $message]);

        $this->get($url)->assertForbidden();
    }

    #[Test]
    public function the_attachment_is_not_reachable_without_a_valid_signature(): void
    {
        Storage::fake('private');

        $customer = User::factory()->create();
        $message = $this->makeMessage($customer, 'tickets/attachments/'.uniqid().'/screenshot.jpg');
        Storage::disk('private')->put($message->attachment_path, 'fake-image-bytes');

        $this->actingAs($customer);

        $this->get(route('ticket-attachments.show', ['message' => $message]))->assertForbidden();
    }

    #[Test]
    public function a_guest_cannot_view_an_attachment_even_with_a_valid_signature(): void
    {
        Storage::fake('private');

        $customer = User::factory()->create();
        $message = $this->makeMessage($customer, 'tickets/attachments/'.uniqid().'/screenshot.jpg');
        Storage::disk('private')->put($message->attachment_path, 'fake-image-bytes');

        $url = URL::signedRoute('ticket-attachments.show', ['message' => $message]);

        $this->get($url)->assertRedirect(route('login'));
    }

    #[Test]
    public function it_returns_404_when_the_attachment_file_does_not_exist_on_disk(): void
    {
        Storage::fake('private');

        $customer = User::factory()->create();
        $message = $this->makeMessage($customer, 'tickets/attachments/'.uniqid().'/missing.jpg');

        $this->actingAs($customer);

        $url = URL::signedRoute('ticket-attachments.show', ['message' => $message]);

        $this->get($url)->assertNotFound();
    }
}
