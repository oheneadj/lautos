<?php

/**
 * Lists all orders for the authenticated customer.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Orders')]
#[Layout('layouts.app')]
class OrderList extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function orders()
    {
        return Auth::user()->orders()
            ->with(['car.make', 'car.carModel', 'car.images'])
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.customer.order-list');
    }
}
