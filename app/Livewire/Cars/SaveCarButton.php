<?php

/**
 * The heart/save toggle button used on car cards and the car detail page.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Cars;

use App\Concerns\BuildsLoginRedirectUrl;
use App\Models\Car;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class SaveCarButton extends Component
{
    use BuildsLoginRedirectUrl;

    public Car $car;

    /**
     * The car card uses the compact icon-only style; the detail page asks
     * for a labelled button so it reads clearly next to "Order This Car".
     */
    public bool $withLabel = false;

    /** Set when a guest is sent to login from the "Login to Save" prompt. */
    #[Url]
    public ?string $intent = null;

    /**
     * Disambiguates which card's save action to resume — a catalogue page
     * renders many SaveCarButton instances, so the uuid says which one.
     */
    #[Url(as: 'car')]
    public ?string $intentCarUuid = null;

    public bool $showLoginPrompt = false;

    /**
     * The page URL the login-prompt modal should send a guest back to. I
     * capture this once at mount time rather than calling request() from
     * the Blade view — after the first wire:click, every subsequent render
     * happens inside Livewire's own AJAX update request, where request()
     * would resolve to that internal endpoint instead of the actual page.
     */
    public string $loginRedirectUrl = '';

    public function mount(Car $car, bool $withLabel = false): void
    {
        $this->car = $car;
        $this->withLabel = $withLabel;
        $this->loginRedirectUrl = $this->buildLoginRedirectUrl(['intent' => 'save', 'car' => $car->uuid]);

        // I use attach(), not toggle(), to resume — if the guest revisits this
        // exact URL later (browser back/forward) after the car is already
        // saved, toggle() would silently un-save it. attach() only ever adds.
        if ($this->intent === 'save' && $this->intentCarUuid === $this->car->uuid && Auth::check() && ! $this->isSaved) {
            Auth::user()->savedCars()->attach($this->car->id);
            unset($this->isSaved);
            $this->forgetSavedCarIdsCache();
        }
    }

    /**
     * I memoize the signed-in user's saved car IDs in the container rather
     * than a static property — a catalogue page mounts one SaveCarButton per
     * card, and without this each one would run its own "is this car saved"
     * query instead of sharing a single fetch. The container binding (unlike
     * a static property) is naturally request-scoped, so it can't leak
     * between unrelated requests or test cases.
     */
    private function savedCarIdsCache(): \Illuminate\Support\Collection
    {
        $key = 'save-car-button.saved-car-ids.'.Auth::id();

        if (! app()->bound($key)) {
            app()->instance($key, Auth::user()->savedCars()->pluck('cars.id'));
        }

        return app($key);
    }

    private function forgetSavedCarIdsCache(): void
    {
        app()->forgetInstance('save-car-button.saved-car-ids.'.Auth::id());
    }

    #[Computed]
    public function isSaved(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->savedCarIdsCache()->contains($this->car->id);
    }

    /**
     * Saves the car if the customer is logged in; otherwise prompts them to
     * log in first rather than silently failing on a null Auth::user().
     */
    public function attemptSave(): void
    {
        if (! Auth::check()) {
            $this->showLoginPrompt = true;

            return;
        }

        $this->toggle();
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
        $this->forgetSavedCarIdsCache();

        $this->dispatch('saved-cars-updated');
    }

    public function render()
    {
        return view('livewire.cars.save-car-button');
    }
}
