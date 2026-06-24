<?php

/**
 * Lists the customer's support tickets. Starting a new one happens through
 * the global support chat bubble/slide-over, not a page-level form here —
 * one place to create a ticket, not two.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Support & Messages')]
#[Layout('layouts.app')]
class SupportTickets extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.customer.support-tickets', [
            'tickets' => Auth::user()->supportTickets()->latest()->paginate(10),
        ]);
    }
}
