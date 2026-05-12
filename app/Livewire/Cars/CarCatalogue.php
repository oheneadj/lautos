<?php

namespace App\Livewire\Cars;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\Make;
use Livewire\Component;
use Livewire\WithPagination;

class CarCatalogue extends Component
{
    use WithPagination;

    public string $search = '';
    public string $makeFilter = '';
    public string $transmissionFilter = '';
    public string $fuelFilter = '';
    public string $minYear = '';
    public string $maxYear = '';
    public string $maxPrice = '';
    public string $sort = 'latest';

    protected $queryString = [
        'search'              => ['except' => ''],
        'makeFilter'          => ['except' => '', 'as' => 'make'],
        'transmissionFilter'  => ['except' => '', 'as' => 'transmission'],
        'fuelFilter'          => ['except' => '', 'as' => 'fuel'],
        'minYear'             => ['except' => '', 'as' => 'min_year'],
        'maxYear'             => ['except' => '', 'as' => 'max_year'],
        'maxPrice'            => ['except' => '', 'as' => 'max_price'],
        'sort'                => ['except' => 'latest'],
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedMakeFilter(): void { $this->resetPage(); }
    public function updatedTransmissionFilter(): void { $this->resetPage(); }
    public function updatedFuelFilter(): void { $this->resetPage(); }
    public function updatedMinYear(): void { $this->resetPage(); }
    public function updatedMaxYear(): void { $this->resetPage(); }
    public function updatedMaxPrice(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->reset(['search', 'makeFilter', 'transmissionFilter', 'fuelFilter', 'minYear', 'maxYear', 'maxPrice']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Car::with(['make', 'carModel', 'carTrim', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(1)])
            ->where('status', CarStatus::Available);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('make', fn ($m) => $m->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('carModel', fn ($m) => $m->where('name', 'like', "%{$this->search}%"))
                    ->orWhere('colour', 'like', "%{$this->search}%")
                    ->orWhere('year', 'like', "%{$this->search}%");
            });
        }

        if ($this->makeFilter) {
            $query->where('make_id', $this->makeFilter);
        }

        if ($this->transmissionFilter) {
            $query->where('transmission', $this->transmissionFilter);
        }

        if ($this->fuelFilter) {
            $query->where('fuel_type', $this->fuelFilter);
        }

        if ($this->minYear) {
            $query->where('year', '>=', $this->minYear);
        }

        if ($this->maxYear) {
            $query->where('year', '<=', $this->maxYear);
        }

        if ($this->maxPrice) {
            $query->where('price_usd_cents', '<=', $this->maxPrice * 100);
        }

        $query->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price_usd_cents'))
            ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('price_usd_cents'))
            ->when($this->sort === 'year_desc', fn ($q) => $q->orderByDesc('year'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest());

        $cars  = $query->paginate(12);
        $makes = Make::orderBy('name')->get();

        return view('livewire.cars.car-catalogue', compact('cars', 'makes'));
    }
}
