<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\SupportTickets;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the support ticket list page. Creating a ticket itself is covered
 * by SupportChatBubbleTest — this page's "New Ticket" button just opens
 * that same slide-over rather than having its own create form.
 */
class SupportTicketsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_guest_cannot_access_the_support_page(): void
    {
        $this->get(route('dashboard.support'))->assertRedirect(route('login'));
    }

    #[Test]
    public function it_lists_the_customers_tickets(): void
    {
        $user = User::factory()->create();
        $user->supportTickets()->create(['subject' => 'My issue', 'status' => 'Open']);

        $this->actingAs($user)->get(route('dashboard.support'))
            ->assertOk()
            ->assertSee('My issue');
    }

    #[Test]
    public function the_new_ticket_button_dispatches_the_open_support_chat_event(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SupportTickets::class)
            ->assertSee('open-support-chat', false);
    }

    #[Test]
    public function it_no_longer_has_its_own_create_ticket_form(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SupportTickets::class)
            ->assertDontSee('wire:submit="createTicket"', false);
    }
}
