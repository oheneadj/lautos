<?php

namespace App\Livewire\Customer;

use App\Models\Car;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Saved Cars')]
#[Layout('layouts.app')]
class SavedCars extends Component
{
    public function removeSavedCar(string $uuid)
    {
        $car = Car::where('uuid', $uuid)->first();
        if ($car) {
            Auth::user()->savedCars()->detach($car->id);
            $this->dispatch('toast', message: __('Car removed from saved list.'));
        }
    }

    public function render()
    {
        return view('livewire.customer.saved-cars', [
            'savedCars' => Auth::user()->savedCars()->with(['make', 'carModel', 'images'])->get(),
        ]);
    }
}
