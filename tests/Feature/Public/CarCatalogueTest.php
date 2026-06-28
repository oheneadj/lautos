<?php

namespace Tests\Feature\Public;

use App\Enums\CarBodyType;
use App\Enums\CarStatus;
use App\Livewire\Cars\CarCatalogue;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the public car catalogue's visibility rules, filters, search, and sort.
 */
class CarCatalogueTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(array $attributes = []): Car
    {
        $make = Make::firstOrCreate(['name' => $attributes['make'] ?? 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => $attributes['model'] ?? 'Corolla']);

        return Car::factory()->create(array_merge([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
        ], array_diff_key($attributes, array_flip(['make', 'model']))));
    }

    #[Test]
    public function the_catalogue_page_loads(): void
    {
        $this->get(route('cars.index'))->assertOk();
    }

    #[Test]
    public function it_shows_available_and_reserved_cars(): void
    {
        $available = $this->makeCar(['status' => CarStatus::Available]);
        $reserved = $this->makeCar(['status' => CarStatus::Reserved]);

        Livewire::test(CarCatalogue::class)
            ->assertSee($available->make->name)
            ->assertSee($reserved->make->name);
    }

    #[Test]
    public function it_shows_a_car_sold_within_the_last_7_days_but_hides_one_sold_longer_ago(): void
    {
        $recentlySold = $this->makeCar(['status' => CarStatus::Sold, 'sold_at' => now()->subDays(3)]);
        $longSold = $this->makeCar(['status' => CarStatus::Sold, 'sold_at' => now()->subDays(10)]);

        Livewire::test(CarCatalogue::class)
            ->assertSee($recentlySold->slug)
            ->assertDontSee($longSold->slug);
    }

    #[Test]
    public function it_filters_by_make_slug(): void
    {
        $toyota = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);
        $honda = $this->makeCar(['make' => 'Honda', 'model' => 'Civic']);

        Livewire::test(CarCatalogue::class)
            ->set('makeFilter', [$toyota->make->slug])
            ->assertSee($toyota->slug)
            ->assertDontSee($honda->slug);
    }

    #[Test]
    public function leaving_any_make_selected_in_the_hero_search_does_not_hide_every_car(): void
    {
        // The hero search's "Any Make" option still submits make[]= (an empty
        // string), which used to land in $makeFilter as [''] and make
        // whereIn('slug', ['']) match nothing — wiping out every car.
        $toyota = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);

        $this->get(route('cars.index', ['make' => ['']]))
            ->assertOk()
            ->assertSee($toyota->slug);
    }

    #[Test]
    public function selecting_a_transmission_from_the_hero_search_still_returns_matches(): void
    {
        // Reproduces the exact hero-form request: "Any Make" left blank
        // alongside a real transmission selection — both are submitted
        // together, so the blank make[] must not blank out the transmission match.
        $manual = $this->makeCar(['transmission' => 'Manual']);
        $automatic = $this->makeCar(['transmission' => 'Automatic']);

        $this->get(route('cars.index', ['make' => [''], 'transmission' => 'Manual']))
            ->assertOk()
            ->assertSee($manual->slug)
            ->assertDontSee($automatic->slug);
    }

    #[Test]
    public function it_filters_by_country_of_origin(): void
    {
        $fromJapan = $this->makeCar(['country_of_origin' => 'Japan']);
        $fromKorea = $this->makeCar(['country_of_origin' => 'Korea']);

        Livewire::test(CarCatalogue::class)
            ->set('countryFilter', 'Japan')
            ->assertSee($fromJapan->slug)
            ->assertDontSee($fromKorea->slug);
    }

    #[Test]
    public function it_filters_by_fuel_type_and_transmission(): void
    {
        $petrolManual = $this->makeCar(['fuel_type' => 'Petrol', 'transmission' => 'Manual']);
        $dieselAuto = $this->makeCar(['fuel_type' => 'Diesel', 'transmission' => 'Automatic']);

        Livewire::test(CarCatalogue::class)
            ->set('fuelFilter', 'Petrol')
            ->set('transmissionFilter', 'Manual')
            ->assertSee($petrolManual->slug)
            ->assertDontSee($dieselAuto->slug);
    }

    #[Test]
    public function it_filters_by_max_price_converted_from_ghs_to_usd(): void
    {
        Setting::set('exchange_rate_usd_to_ghs', '15');

        $cheap = $this->makeCar(['price_usd_cents' => 1000000]); // $10,000 = GH₵150,000
        $expensive = $this->makeCar(['price_usd_cents' => 5000000]); // $50,000 = GH₵750,000

        Livewire::test(CarCatalogue::class)
            ->set('maxPriceGhs', 200000)
            ->assertSee($cheap->slug)
            ->assertDontSee($expensive->slug);
    }

    #[Test]
    public function it_searches_by_make_model_and_colour(): void
    {
        $match = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);
        $other = $this->makeCar(['make' => 'Honda', 'model' => 'Civic']);

        Livewire::test(CarCatalogue::class)
            ->set('search', 'Corolla')
            ->assertSee($match->slug)
            ->assertDontSee($other->slug);
    }

    #[Test]
    public function it_shows_an_empty_state_when_no_cars_match(): void
    {
        Livewire::test(CarCatalogue::class)
            ->set('search', 'NoSuchCarExists')
            ->assertSee('No cars match your search');
    }

    #[Test]
    public function it_sorts_by_price_ascending(): void
    {
        $cheap = $this->makeCar(['price_usd_cents' => 500000]);
        $expensive = $this->makeCar(['price_usd_cents' => 4000000]);

        $component = Livewire::test(CarCatalogue::class)->set('sort', 'price_asc');

        $cars = $component->viewData('cars');

        $this->assertTrue($cars->first()->is($cheap));
        $this->assertTrue($cars->last()->is($expensive));
    }

    #[Test]
    public function the_mobile_filter_drawer_starts_closed_and_opens_on_demand(): void
    {
        Livewire::test(CarCatalogue::class)
            ->assertSet('showMobileFilters', false)
            ->set('showMobileFilters', true)
            ->assertSet('showMobileFilters', true)
            ->assertSeeHtml('wire:click="$set(\'showMobileFilters\', false)"');
    }

    #[Test]
    public function it_filters_by_multiple_makes_at_once(): void
    {
        $toyota = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);
        $honda = $this->makeCar(['make' => 'Honda', 'model' => 'Civic']);
        $kia = $this->makeCar(['make' => 'Kia', 'model' => 'Sportage']);

        Livewire::test(CarCatalogue::class)
            ->set('makeFilter', [$toyota->make->slug, $honda->make->slug])
            ->assertSee($toyota->slug)
            ->assertSee($honda->slug)
            ->assertDontSee($kia->slug);
    }

    #[Test]
    public function it_filters_by_body_type(): void
    {
        $suv = $this->makeCar(['body_type' => CarBodyType::Suv]);
        $sedan = $this->makeCar(['body_type' => CarBodyType::Sedan]);

        Livewire::test(CarCatalogue::class)
            ->set('bodyTypeFilter', [CarBodyType::Suv->value])
            ->assertSee($suv->slug)
            ->assertDontSee($sedan->slug);
    }

    #[Test]
    public function an_unrecognised_body_type_in_the_url_does_not_crash_the_page(): void
    {
        // CarBodyType::from() throws on an unknown value — a crafted URL like
        // ?body_type[0]=zzz must not take the whole catalogue page down.
        Livewire::test(CarCatalogue::class)
            ->set('bodyTypeFilter', ['zzz'])
            ->assertOk();
    }

    #[Test]
    public function an_unrecognised_make_slug_in_the_url_is_silently_ignored(): void
    {
        $toyota = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);

        Livewire::test(CarCatalogue::class)
            ->set('makeFilter', ['not-a-real-make', $toyota->make->slug])
            ->assertOk()
            ->assertDontSee('not-a-real-make')
            ->assertSee('Toyota');
    }

    #[Test]
    public function removing_a_body_type_chip_clears_just_that_body_type(): void
    {
        $suv = $this->makeCar(['body_type' => CarBodyType::Suv]);
        $sedan = $this->makeCar(['body_type' => CarBodyType::Sedan]);

        Livewire::test(CarCatalogue::class)
            ->set('bodyTypeFilter', [CarBodyType::Suv->value, CarBodyType::Sedan->value])
            ->call('removeBodyType', CarBodyType::Suv->value)
            ->assertSet('bodyTypeFilter', [CarBodyType::Sedan->value])
            ->assertDontSee($suv->slug)
            ->assertSee($sedan->slug);
    }

    #[Test]
    public function selecting_a_make_reveals_only_that_makes_models(): void
    {
        $corolla = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);
        $civic = $this->makeCar(['make' => 'Honda', 'model' => 'Civic']);

        $component = Livewire::test(CarCatalogue::class)
            ->set('makeFilter', [$corolla->make->slug]);

        $models = $component->viewData('models');

        $this->assertTrue($models->contains('name', 'Corolla'));
        $this->assertFalse($models->contains('name', 'Civic'));
    }

    #[Test]
    public function changing_the_make_filter_clears_the_model_filter(): void
    {
        $corolla = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);

        Livewire::test(CarCatalogue::class)
            ->set('makeFilter', [$corolla->make->slug])
            ->set('modelFilter', ['Corolla'])
            ->set('makeFilter', [])
            ->assertSet('modelFilter', []);
    }

    #[Test]
    public function it_filters_by_model_name(): void
    {
        $corolla = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);
        $camry = $this->makeCar(['make' => 'Toyota', 'model' => 'Camry']);

        Livewire::test(CarCatalogue::class)
            ->set('makeFilter', [$corolla->make->slug])
            ->set('modelFilter', ['Corolla'])
            ->assertSee($corolla->slug)
            ->assertDontSee($camry->slug);
    }

    #[Test]
    public function it_filters_by_mileage_range(): void
    {
        $lowMileage = $this->makeCar(['mileage' => 20000]);
        $highMileage = $this->makeCar(['mileage' => 180000]);

        Livewire::test(CarCatalogue::class)
            ->set('maxMileage', 50000)
            ->assertSee($lowMileage->slug)
            ->assertDontSee($highMileage->slug);
    }

    #[Test]
    public function pushing_the_mileage_slider_to_its_cap_clears_the_filter(): void
    {
        $highMileage = $this->makeCar(['mileage' => 280000]);

        Livewire::test(CarCatalogue::class)
            ->set('maxMileage', CarCatalogue::MAX_MILEAGE_CAP)
            ->assertSet('maxMileage', '')
            ->assertSee($highMileage->slug);
    }

    #[Test]
    public function removing_a_make_chip_clears_just_that_make(): void
    {
        $toyota = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);
        $honda = $this->makeCar(['make' => 'Honda', 'model' => 'Civic']);

        Livewire::test(CarCatalogue::class)
            ->set('makeFilter', [$toyota->make->slug, $honda->make->slug])
            ->call('removeMake', $toyota->make->slug)
            ->assertSet('makeFilter', [$honda->make->slug])
            ->assertDontSee($toyota->slug)
            ->assertSee($honda->slug);
    }

    #[Test]
    public function the_make_checkbox_count_reflects_other_active_filters(): void
    {
        $toyotaPetrol = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla', 'fuel_type' => 'Petrol']);
        $toyotaDiesel = $this->makeCar(['make' => 'Toyota', 'model' => 'Camry', 'fuel_type' => 'Diesel']);

        $component = Livewire::test(CarCatalogue::class)
            ->set('fuelFilter', 'Petrol');

        $makeCounts = $component->viewData('makeCounts');

        $this->assertSame(1, $makeCounts[$toyotaPetrol->make_id]);
    }

    #[Test]
    public function clear_all_resets_every_filter_including_the_new_ones(): void
    {
        $corolla = $this->makeCar(['make' => 'Toyota', 'model' => 'Corolla']);

        Livewire::test(CarCatalogue::class)
            ->set('makeFilter', [$corolla->make->slug])
            ->set('modelFilter', ['Corolla'])
            ->set('bodyTypeFilter', [CarBodyType::Suv->value])
            ->set('maxMileage', 50000)
            ->call('clearFilters')
            ->assertSet('makeFilter', [])
            ->assertSet('modelFilter', [])
            ->assertSet('bodyTypeFilter', [])
            ->assertSet('maxMileage', '');
    }
}
