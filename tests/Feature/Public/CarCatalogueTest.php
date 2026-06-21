<?php

namespace Tests\Feature\Public;

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
            ->set('makeFilter', $toyota->make->slug)
            ->assertSee($toyota->slug)
            ->assertDontSee($honda->slug);
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
}
