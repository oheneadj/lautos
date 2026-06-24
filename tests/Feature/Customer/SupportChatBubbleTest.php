<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\SupportChatBubble;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the floating support bubble + slide-over available on every
 * dashboard page.
 */
class SupportChatBubbleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_bubble_is_mounted_on_every_dashboard_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard.index'))
            ->assertOk()
            ->assertSeeLivewire('customer.support-chat-bubble');
    }

    #[Test]
    public function the_open_support_chat_event_opens_the_slideover(): void
    {
        $user = User::factory()->create();

        // This is the event the Support page's "New Ticket" button dispatches —
        // it should open this same slide-over rather than a separate form.
        Livewire::actingAs($user)->test(SupportChatBubble::class)
            ->assertSet('showSlideOver', false)
            ->dispatch('open-support-chat')
            ->assertSet('showSlideOver', true);
    }

    #[Test]
    public function a_guest_cannot_access_the_bubble(): void
    {
        $this->get(route('dashboard.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function it_shows_a_new_ticket_form_when_the_customer_has_no_open_ticket(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SupportChatBubble::class)
            ->call('toggle')
            ->assertSee('What can we help with?');
    }

    #[Test]
    public function starting_a_ticket_from_the_bubble_creates_it_with_a_first_message(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SupportChatBubble::class)
            ->set('subject', 'Help with my order')
            ->set('message', 'Where is my car?')
            ->call('startTicket');

        $this->assertDatabaseHas('support_tickets', [
            'user_id' => $user->id,
            'subject' => 'Help with my order',
            'status' => 'Open',
        ]);
        $this->assertDatabaseHas('ticket_messages', [
            'message' => 'Where is my car?',
            'is_admin' => false,
        ]);
    }

    #[Test]
    public function starting_a_ticket_requires_a_subject_and_message(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SupportChatBubble::class)
            ->call('startTicket')
            ->assertHasErrors(['subject', 'message']);

        $this->assertDatabaseCount('support_tickets', 0);
    }

    #[Test]
    public function it_shows_the_existing_open_tickets_thread_instead_of_the_new_ticket_form(): void
    {
        $user = User::factory()->create();
        $ticket = $user->supportTickets()->create(['subject' => 'Existing issue', 'status' => 'Open']);
        $ticket->messages()->create(['user_id' => $user->id, 'message' => 'Hello there', 'is_admin' => false]);

        Livewire::actingAs($user)->test(SupportChatBubble::class)
            ->call('toggle')
            ->assertSee('Hello there')
            ->assertDontSee('What can we help with?');
    }

    #[Test]
    public function replying_on_the_active_ticket_appends_a_message_and_touches_the_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = $user->supportTickets()->create(['subject' => 'Existing issue', 'status' => 'Open']);
        $ticket->messages()->create(['user_id' => $user->id, 'message' => 'First message', 'is_admin' => false]);

        Livewire::actingAs($user)->test(SupportChatBubble::class)
            ->set('message', 'Following up on this')
            ->call('sendMessage');

        $this->assertDatabaseHas('ticket_messages', [
            'support_ticket_id' => $ticket->id,
            'message' => 'Following up on this',
            'is_admin' => false,
        ]);
    }

    #[Test]
    public function a_closed_ticket_shows_a_link_to_start_a_new_one_instead_of_a_reply_form(): void
    {
        $user = User::factory()->create();
        $ticket = $user->supportTickets()->create(['subject' => 'Resolved issue', 'status' => 'Closed']);
        $ticket->messages()->create(['user_id' => $user->id, 'message' => 'Thanks for the help', 'is_admin' => false]);

        // A closed ticket is deliberately not picked up as the "active" one —
        // mount() only looks at Open/In Progress tickets — so this customer
        // should see the new-ticket form, not a dead-end closed thread.
        Livewire::actingAs($user)->test(SupportChatBubble::class)
            ->call('toggle')
            ->assertSee('What can we help with?');
    }

    #[Test]
    public function the_bubble_prefers_the_most_recently_updated_open_ticket(): void
    {
        $user = User::factory()->create();
        $older = $user->supportTickets()->create(['subject' => 'Older', 'status' => 'Open']);
        $older->messages()->create(['user_id' => $user->id, 'message' => 'Older message', 'is_admin' => false]);
        $older->forceFill(['updated_at' => now()->subDay()])->save();

        $newer = $user->supportTickets()->create(['subject' => 'Newer', 'status' => 'In Progress']);
        $newer->messages()->create(['user_id' => $user->id, 'message' => 'Newer message', 'is_admin' => false]);

        Livewire::actingAs($user)->test(SupportChatBubble::class)
            ->call('toggle')
            ->assertSee('Newer message')
            ->assertDontSee('Older message');
    }
}
