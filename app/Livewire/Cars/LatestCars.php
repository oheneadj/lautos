<?php

namespace App\Livewire\Cars;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Models\Car;
use Livewire\Component;

class LatestCars extends Component
{
    public function render()
    {
        $cars = Car::with(['make', 'carModel', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(1)])
            // I exclude Cancelled orders from the count — those lost the race to another
            // buyer's confirmed payment, so they shouldn't inflate the reservation badge.
            ->withCount(['orders' => fn ($q) => $q->where('status', '!=', OrderStatus::Cancelled)])
            ->where('status', CarStatus::Available)
            ->latest()
            ->limit(6)
            ->get();

        return view('livewire.cars.latest-cars', compact('cars'));
    }
}
