<?php

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Invoices & Billing')]
#[Layout('layouts.app')]
class Invoices extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.customer.invoices', [
            'orders' => Auth::user()->orders()->with(['car.make', 'car.carModel', 'paymentProofs'])->latest()->paginate(10),
        ]);
    }
}
