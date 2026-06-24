<?php

namespace App\Livewire\Customer;

use App\Models\Car;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Saved Cars')]
#[Layout('layouts.app')]
class SavedCars extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sort = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'sort'   => ['except' => 'latest'],
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedSort(): void { $this->resetPage(); }

    #[On('saved-cars-updated')]
    public function refreshCars(): void
    {
        // Simply listening to the event triggers a re-render.
    }

    public function render()
    {
        $query = Auth::user()->savedCars()->with(['make', 'carModel', 'carTrim', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(1)]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('make', fn ($m) => $m->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('carModel', fn ($m) => $m->where('name', 'like', "%{$this->search}%"))
                    ->orWhere('year', 'like', "%{$this->search}%");
            });
        }

        $query->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price_usd_cents'))
            ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('price_usd_cents'))
            ->when($this->sort === 'year_desc', fn ($q) => $q->orderByDesc('year'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest('car_user.created_at'));

        return view('livewire.customer.saved-cars', [
            'savedCars' => $query->paginate(12),
        ]);
    }
}
