<?php

/**
 * Powers the public car catalogue — search, filtering, sorting, and pagination.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Cars;

use App\Enums\CarBodyType;
use App\Enums\OrderStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class CarCatalogue extends Component
{
    use WithPagination;

    /**
     * The mileage slider's rightmost position — landing here means "no
     * mileage cap", matching the reference UI's "250,000+ miles" end label.
     */
    public const MAX_MILEAGE_CAP = 300000;

    public string $search = '';

    /** @var array<int, string> Make slugs — multi-select, like the rest of the filter facets. */
    public array $makeFilter = [];

    /** @var array<int, string> Model names, only meaningful once at least one make is selected. */
    public array $modelFilter = [];

    /** @var array<int, string> Body type values — multi-select, same pattern as makeFilter. */
    public array $bodyTypeFilter = [];

    public string $transmissionFilter = '';
    public string $fuelFilter = '';
    public string $countryFilter = '';
    public string $minYear = '';
    public string $maxYear = '';
    public string $maxPriceGhs = '';
    public string $maxMileage = '';
    public string $sort = 'latest';

    // Purely transient UI state for the mobile filter drawer — not worth
    // persisting in the URL, it should always start closed on page load.
    public bool $showMobileFilters = false;

    protected $queryString = [
        'search'              => ['except' => ''],
        'makeFilter'          => ['except' => [], 'as' => 'make'],
        'modelFilter'         => ['except' => [], 'as' => 'model'],
        'bodyTypeFilter'      => ['except' => [], 'as' => 'body_type'],
        'transmissionFilter'  => ['except' => '', 'as' => 'transmission'],
        'fuelFilter'          => ['except' => '', 'as' => 'fuel'],
        'countryFilter'       => ['except' => '', 'as' => 'country'],
        'minYear'             => ['except' => '', 'as' => 'min_year'],
        'maxYear'             => ['except' => '', 'as' => 'max_year'],
        'maxPriceGhs'         => ['except' => '', 'as' => 'max_price'],
        'maxMileage'          => ['except' => '', 'as' => 'max_mileage'],
        'sort'                => ['except' => 'latest'],
    ];

    public function updatedSearch(): void { $this->resetPage(); }

    public function updatedMakeFilter(): void
    {
        // Model is scoped to whichever make(s) are selected — once the make
        // selection changes there's no guarantee the previously-picked
        // models still belong to it, so I just clear it rather than try to
        // reconcile which ones still apply.
        $this->modelFilter = [];
        $this->resetPage();
    }

    public function updatedModelFilter(): void { $this->resetPage(); }
    public function updatedBodyTypeFilter(): void { $this->resetPage(); }
    public function updatedTransmissionFilter(): void { $this->resetPage(); }
    public function updatedFuelFilter(): void { $this->resetPage(); }
    public function updatedCountryFilter(): void { $this->resetPage(); }
    public function updatedMinYear(): void { $this->resetPage(); }
    public function updatedMaxYear(): void { $this->resetPage(); }
    public function updatedMaxPriceGhs(): void { $this->resetPage(); }

    public function updatedMaxMileage(): void
    {
        // The slider's rightmost notch represents "no cap" — I normalise it
        // back to an empty string so filteredQuery() doesn't apply a
        // literal "mileage <= 300000" constraint that would hide nothing
        // but isn't really "no filter" either.
        if ((int) $this->maxMileage >= self::MAX_MILEAGE_CAP) {
            $this->maxMileage = '';
        }

        $this->resetPage();
    }

    /** Removes a single make from the filter — used by the active-filter chips. */
    public function removeMake(string $slug): void
    {
        $this->makeFilter = array_values(array_diff($this->makeFilter, [$slug]));
        $this->modelFilter = [];
        $this->resetPage();
    }

    /** Removes a single model from the filter — used by the active-filter chips. */
    public function removeModel(string $name): void
    {
        $this->modelFilter = array_values(array_diff($this->modelFilter, [$name]));
        $this->resetPage();
    }

    /** Removes a single body type from the filter — used by the active-filter chips. */
    public function removeBodyType(string $value): void
    {
        $this->bodyTypeFilter = array_values(array_diff($this->bodyTypeFilter, [$value]));
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search', 'makeFilter', 'modelFilter', 'bodyTypeFilter', 'transmissionFilter', 'fuelFilter',
            'countryFilter', 'minYear', 'maxYear', 'maxPriceGhs', 'maxMileage',
        ]);
        $this->resetPage();
    }

    /**
     * Builds the catalogue query with every filter applied except the ones
     * named in $exclude — this is what lets the Make and Model checkbox
     * counts reflect "how many cars match if I also picked this one", the
     * standard faceted-search behaviour, rather than just a flat total.
     *
     * @param  array<int, string>  $exclude
     */
    private function filteredQuery(array $exclude = []): Builder
    {
        $query = Car::visibleOnCatalogue();

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('make', fn ($m) => $m->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('carModel', fn ($m) => $m->where('name', 'like', "%{$this->search}%"))
                    ->orWhere('colour', 'like', "%{$this->search}%")
                    ->orWhere('year', 'like', "%{$this->search}%");
            });
        }

        if (! in_array('make', $exclude, true) && ! empty($this->makeFilter)) {
            // I filter by slug, not id, so the URL and the filter checkboxes both use a readable value.
            $query->whereHas('make', fn ($m) => $m->whereIn('slug', $this->makeFilter));
        }

        if (! in_array('model', $exclude, true) && ! empty($this->modelFilter)) {
            $query->whereHas('carModel', fn ($m) => $m->whereIn('name', $this->modelFilter));
        }

        if (! in_array('bodyType', $exclude, true) && ! empty($this->bodyTypeFilter)) {
            $query->whereIn('body_type', $this->bodyTypeFilter);
        }

        if ($this->transmissionFilter) {
            $query->where('transmission', $this->transmissionFilter);
        }

        if ($this->fuelFilter) {
            $query->where('fuel_type', $this->fuelFilter);
        }

        if ($this->countryFilter) {
            $query->where('country_of_origin', $this->countryFilter);
        }

        if ($this->minYear) {
            $query->where('year', '>=', $this->minYear);
        }

        if ($this->maxYear) {
            $query->where('year', '<=', $this->maxYear);
        }

        if ($this->maxPriceGhs) {
            // I filter on the GHS amount the visitor entered, converted back to the USD cents we actually store.
            $maxPriceUsdCents = ((float) $this->maxPriceGhs / Car::currentExchangeRate()) * 100;
            $query->where('price_usd_cents', '<=', $maxPriceUsdCents);
        }

        if ($this->maxMileage) {
            $query->where('mileage', '<=', $this->maxMileage);
        }

        return $query;
    }

    public function render()
    {
        $query = $this->filteredQuery()
            // I cap at 5 (not 1) so the card's image slider has photos to cycle through,
            // while still avoiding loading a car's entire, possibly much larger, photo set.
            ->with(['make', 'carModel', 'carTrim', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(5)])
            // I exclude Cancelled orders from the count — those lost the race to another
            // buyer's confirmed payment, so they shouldn't inflate the reservation badge.
            ->withCount(['orders' => fn ($q) => $q->where('status', '!=', OrderStatus::Cancelled)]);

        $query->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price_usd_cents'))
            ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('price_usd_cents'))
            ->when($this->sort === 'year_desc', fn ($q) => $q->orderByDesc('year'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest());

        $cars  = $query->paginate(12);
        $makes = Make::orderBy('name')->get();

        // I count "if I also picked this make" against every OTHER active
        // filter, not the unfiltered total — that's what makes the numbers
        // next to each checkbox actually mean something.
        $makeCounts = $this->filteredQuery(['make'])
            ->select('make_id')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('make_id')
            ->pluck('aggregate', 'make_id');

        $models = collect();
        $modelCounts = collect();

        if (! empty($this->makeFilter)) {
            $selectedMakeIds = Make::whereIn('slug', $this->makeFilter)->pluck('id');

            $models = CarModel::whereIn('make_id', $selectedMakeIds)->orderBy('name')->get();

            $modelCounts = $this->filteredQuery(['model'])
                ->select('car_model_id')
                ->selectRaw('count(*) as aggregate')
                ->groupBy('car_model_id')
                ->pluck('aggregate', 'car_model_id');
        }

        $bodyTypeCounts = $this->filteredQuery(['bodyType'])
            ->select('body_type')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('body_type')
            ->pluck('aggregate', 'body_type');

        return view('livewire.cars.car-catalogue', compact('cars', 'makes', 'makeCounts', 'models', 'modelCounts', 'bodyTypeCounts'));
    }
}
