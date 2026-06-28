<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\SupportTicketDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests sending a message (with an optional attachment) on a support ticket.
 */
class SupportTicketDetailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_customer_can_send_a_message_with_an_image_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $ticket = $user->supportTickets()->create(['subject' => 'My issue', 'status' => 'Open']);

        Livewire::actingAs($user)
            ->test(SupportTicketDetail::class, ['uuid' => $ticket->uuid])
            ->set('message', 'Here is a screenshot.')
            ->set('attachment', UploadedFile::fake()->image('screenshot.jpg'))
            ->call('sendMessage')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ticket_messages', ['message' => 'Here is a screenshot.']);
    }

    #[Test]
    public function an_svg_attachment_is_rejected(): void
    {
        // .svg can carry an embedded <script> payload — this is the
        // stored-XSS vector the mimes rule closes.
        Storage::fake('public');

        $user = User::factory()->create();
        $ticket = $user->supportTickets()->create(['subject' => 'My issue', 'status' => 'Open']);

        Livewire::actingAs($user)
            ->test(SupportTicketDetail::class, ['uuid' => $ticket->uuid])
            ->set('message', 'Here is a file.')
            ->set('attachment', UploadedFile::fake()->create('payload.svg', 10, 'image/svg+xml'))
            ->call('sendMessage')
            ->assertHasErrors('attachment');
    }

    #[Test]
    public function an_html_attachment_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $ticket = $user->supportTickets()->create(['subject' => 'My issue', 'status' => 'Open']);

        Livewire::actingAs($user)
            ->test(SupportTicketDetail::class, ['uuid' => $ticket->uuid])
            ->set('message', 'Here is a file.')
            ->set('attachment', UploadedFile::fake()->create('payload.html', 10, 'text/html'))
            ->call('sendMessage')
            ->assertHasErrors('attachment');
    }
}
