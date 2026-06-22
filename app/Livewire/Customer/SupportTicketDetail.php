<?php

namespace App\Livewire\Customer;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Ticket Details')]
#[Layout('layouts.app')]
class SupportTicketDetail extends Component
{
    use WithFileUploads;

    public SupportTicket $ticket;
    public $message = '';
    public $attachment;

    protected $rules = [
        'message' => 'required|string',
        'attachment' => 'nullable|file|max:5120', // 5MB Max
    ];

    public function mount($uuid)
    {
        $this->ticket = Auth::user()->supportTickets()->where('uuid', $uuid)->firstOrFail();
    }

    public function sendMessage()
    {
        $this->validate();

        $path = null;
        if ($this->attachment) {
            $path = $this->attachment->store('tickets/attachments', 'public');
        }

        $this->ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_admin' => false,
            'attachment_path' => $path,
        ]);

        $this->ticket->touch();

        $this->reset(['message', 'attachment']);
        $this->dispatch('toast', message: __('Message sent.'));
    }

    public function render()
    {
        return view('livewire.customer.support-ticket-detail', [
            'messages' => $this->ticket->messages()->with('user')->oldest()->get(),
        ]);
    }
}
