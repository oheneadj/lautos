<?php

/**
 * Floating support bubble available on every dashboard page. Opens a
 * slide-over showing the customer's most recent open conversation, or a
 * quick form to start one if they've never opened a ticket — the full
 * ticket list/history still lives on the dedicated Support page.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Customer;

use App\Models\SupportTicket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class SupportChatBubble extends Component
{
    public bool $showSlideOver = false;
    public ?SupportTicket $activeTicket = null;
    public string $subject = '';
    public string $message = '';

    public function mount(): void
    {
        // I order by updated_at, not created_at — a ticket gets touch()'d
        // every time a new message lands on it, so this picks up whichever
        // conversation is actually most recently active, not just the
        // oldest-opened one.
        $this->activeTicket = Auth::user()->supportTickets()
            ->whereIn('status', ['Open', 'In Progress'])
            ->latest('updated_at')
            ->first();
    }

    public function toggle(): void
    {
        $this->showSlideOver = ! $this->showSlideOver;
    }

    /**
     * Lets the "New Ticket" button on the dedicated Support page open this
     * same slide-over rather than a second, separate create-ticket form —
     * one place to start a conversation, not two.
     */
    #[On('open-support-chat')]
    public function openSlideOver(): void
    {
        $this->showSlideOver = true;
    }

    /**
     * Starts a new ticket from the bubble when the customer has no open
     * conversation yet — this is the only place a ticket gets created from,
     * whether the bubble was opened directly or via the Support page's
     * "New Ticket" button (open-support-chat just opens this same form).
     */
    public function startTicket(): void
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $this->subject,
            'status' => 'Open',
        ]);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_admin' => false,
        ]);

        $this->activeTicket = $ticket;
        $this->reset(['subject', 'message']);
        $this->dispatch('toast', message: __('Ticket created successfully.'));
    }

    /** Replies on the active ticket's thread. */
    public function sendMessage(): void
    {
        $this->validate(['message' => 'required|string']);

        $this->activeTicket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_admin' => false,
        ]);

        $this->activeTicket->touch();
        $this->reset(['message']);
    }

    /**
     * Named threadMessages, not messages — Livewire reserves a messages()
     * method on components for custom validation error messages (mirroring
     * Laravel's FormRequest convention), and colliding with it makes
     * validate() crash trying to array_merge a Collection into its rules.
     */
    #[Computed]
    public function threadMessages(): Collection
    {
        if (! $this->activeTicket) {
            return collect();
        }

        return $this->activeTicket->messages()->with('user')->oldest()->get();
    }

    public function render()
    {
        return view('livewire.customer.support-chat-bubble');
    }
}
