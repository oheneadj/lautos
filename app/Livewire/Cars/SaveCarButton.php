<?php

/**
 * The heart/save toggle button used on car cards and the car detail page.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Cars;

use App\Models\Car;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SaveCarButton extends Component
{
    public Car $car;

    /**
     * The car card uses the compact icon-only style; the detail page asks
     * for a labelled button so it reads clearly next to "Order This Car".
     */
    public bool $withLabel = false;

    #[Computed]
    public function isSaved(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->savedCars()->where('car_id', $this->car->id)->exists();
    }

    /**
     * Toggles the saved state for this car. I use the pivot's own toggle()
     * method rather than checking isSaved() first and branching myself —
     * it's a single atomic query so double-clicks can't attach the same
     * car twice or fight each other.
     */
    public function toggle(): void
    {
        Auth::user()->savedCars()->toggle($this->car->id);

        unset($this->isSaved);
    }

    public function render()
    {
        return view('livewire.cars.save-car-button');
    }
}
