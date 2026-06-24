<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin support ticket resource — viewing, replying, and
 * toggling status, which previously had no admin UI at all.
 */
class SupportTicketManagementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($user);

        return $user;
    }

    private function makeTicket(array $attributes = []): SupportTicket
    {
        $customer = User::factory()->create();

        return SupportTicket::create(array_merge([
            'user_id' => $customer->id,
            'subject' => 'My car has not arrived',
            'status' => 'Open',
        ], $attributes));
    }

    #[Test]
    public function guest_cannot_access_support_ticket_management(): void
    {
        $this->get('/admin/support-tickets')->assertRedirect('/admin/login');
    }

    #[Test]
    public function admin_can_see_tickets_with_status_badge(): void
    {
        $this->actingAsAdmin();

        $ticket = $this->makeTicket(['subject' => 'Payment proof not accepted']);

        Livewire::test(ListSupportTickets::class)
            ->assertCanSeeTableRecords([$ticket])
            ->assertSee('Payment proof not accepted')
            ->assertSee('Open');
    }

    #[Test]
    public function admin_can_view_a_ticket_and_see_its_messages(): void
    {
        $this->actingAsAdmin();

        $ticket = $this->makeTicket();
        $ticket->messages()->create([
            'user_id' => $ticket->user_id,
            'message' => 'When will my car arrive?',
            'is_admin' => false,
        ]);

        Livewire::test(ViewSupportTicket::class, ['record' => $ticket->uuid])
            ->assertOk()
            ->assertSee('When will my car arrive?');
    }

    #[Test]
    public function admin_can_reply_and_the_ticket_moves_to_in_progress(): void
    {
        $this->actingAsAdmin();

        $ticket = $this->makeTicket(['status' => 'Open']);

        Livewire::test(ViewSupportTicket::class, ['record' => $ticket->uuid])
            ->callAction('reply', data: ['message' => 'Your car is currently at sea, ETA next week.']);

        $this->assertDatabaseHas('ticket_messages', [
            'support_ticket_id' => $ticket->id,
            'message' => 'Your car is currently at sea, ETA next week.',
            'is_admin' => true,
        ]);
        $this->assertSame('In Progress', $ticket->refresh()->status);
    }

    #[Test]
    public function admin_can_close_and_reopen_a_ticket(): void
    {
        $this->actingAsAdmin();

        $ticket = $this->makeTicket(['status' => 'In Progress']);

        Livewire::test(ViewSupportTicket::class, ['record' => $ticket->uuid])
            ->callAction('closeTicket');

        $this->assertSame('Closed', $ticket->refresh()->status);

        Livewire::test(ViewSupportTicket::class, ['record' => $ticket->uuid])
            ->callAction('reopenTicket');

        $this->assertSame('Open', $ticket->refresh()->status);
    }

    #[Test]
    public function it_filters_tickets_by_status(): void
    {
        $this->actingAsAdmin();

        $open = $this->makeTicket(['status' => 'Open']);
        $closed = $this->makeTicket(['status' => 'Closed']);

        Livewire::test(ListSupportTickets::class)
            ->filterTable('status', 'Open')
            ->assertCanSeeTableRecords([$open])
            ->assertCanNotSeeTableRecords([$closed]);
    }

    #[Test]
    public function status_tabs_filter_the_table_to_just_that_status(): void
    {
        $this->actingAsAdmin();

        $open = $this->makeTicket(['status' => 'Open']);
        $closed = $this->makeTicket(['status' => 'Closed']);

        Livewire::test(ListSupportTickets::class)
            ->set('activeTab', 'Closed')
            ->assertCanSeeTableRecords([$closed])
            ->assertCanNotSeeTableRecords([$open]);
    }
}
