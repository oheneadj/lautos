<?php

namespace App\Livewire\Cars;

use App\Models\Car;
use Livewire\Component;

class CarDetail extends Component
{
    public Car $car;
    public int $activeImageIndex = 0;

    public function setActiveImage(int $index): void
    {
        $this->activeImageIndex = $index;
    }

    public function render()
    {
        return view('livewire.cars.car-detail');
    }
}
