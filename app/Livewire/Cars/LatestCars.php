<?php

namespace App\Livewire\Cars;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Models\Car;
use Livewire\Component;

class LatestCars extends Component
{
    /**
     * I show a skeleton grid while this lazy-loads, so the page doesn't jump
     * once the real cars arrive.
     */
    public function placeholder(): string
    {
        return <<<'HTML'
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="h-[340px] rounded-lg bg-gray-100 animate-pulse"></div>
                <div class="h-[340px] rounded-lg bg-gray-100 animate-pulse"></div>
                <div class="h-[340px] rounded-lg bg-gray-100 animate-pulse"></div>
            </div>
            HTML;
    }

    public function render()
    {
        // I cap at 5 (not 1) so the card's image slider has photos to cycle through,
        // while still avoiding loading a car's entire, possibly much larger, photo set.
        $cars = Car::with(['make', 'carModel', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(5)])
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
