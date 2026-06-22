<?php

namespace App\Livewire\Customer;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Support & Messages')]
#[Layout('layouts.app')]
class SupportTickets extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $subject = '';
    public $message = '';

    protected $rules = [
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ];

    public function createTicket()
    {
        $this->validate();

        $ticket = SupportTicket::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'subject' => $this->subject,
            'status' => 'Open',
        ]);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_admin' => false,
        ]);

        $this->reset(['subject', 'message', 'showCreateModal']);
        $this->dispatch('toast', message: __('Ticket created successfully.'));
    }

    public function render()
    {
        return view('livewire.customer.support-tickets', [
            'tickets' => Auth::user()->supportTickets()->latest()->paginate(10),
        ]);
    }
}
