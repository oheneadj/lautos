<?php

namespace App\Livewire\Cars;

use App\Enums\CarStatus;
use App\Models\Car;
use Livewire\Component;

class LatestCars extends Component
{
    public function render()
    {
        $cars = Car::with(['make', 'carModel', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(1)])
            ->where('status', CarStatus::Available)
            ->latest()
            ->limit(6)
            ->get();

        return view('livewire.cars.latest-cars', compact('cars'));
    }
}
