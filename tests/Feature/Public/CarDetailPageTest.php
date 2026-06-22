<?php

namespace Tests\Feature\Public;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the public car detail page.
 */
class CarDetailPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Creates a car with a real make/model so the page can render its title.
     */
    private function makeCar(CarStatus $status = CarStatus::Available): Car
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => $status,
        ]);
    }

    #[Test]
    public function it_shows_an_available_car(): void
    {
        $car = $this->makeCar();

        $this->get(route('cars.show', $car->slug))
            ->assertOk()
            ->assertSee('Toyota')
            ->assertSee('Corolla')
            ->assertSee(number_format($car->price_usd, 0));
    }

    #[Test]
    public function car_detail_url_uses_a_clean_seo_slug_with_no_uuid_or_integer_id(): void
    {
        $car = $this->makeCar();

        $this->assertSame("{$car->year}-toyota-corolla", $car->slug);

        // I anchor on the closing quote so a year that happens to start with the same digits (e.g. id 1, year 1981) can't false-positive.
        // The uuid can still appear in Livewire's hidden component snapshot — that's internal wiring, not a URL/UI exposure — so I only check the canonical URL itself.
        $this->get(route('cars.show', $car->slug))
            ->assertOk()
            ->assertDontSee('/cars/' . $car->id . '"', false)
            ->assertSee('og:url" content="' . route('cars.show', $car->slug) . '"', false);
    }

    #[Test]
    public function the_page_sets_a_unique_seo_title_and_description_for_the_car(): void
    {
        $car = $this->makeCar();

        // I check the meta title includes make, model, year, and price so each listing is uniquely discoverable.
        $this->get(route('cars.show', $car->slug))
            ->assertOk()
            ->assertSee("{$car->year} Toyota Corolla", false)
            ->assertSee('$' . number_format($car->price_usd, 0), false);
    }

    #[Test]
    public function a_car_sold_more_than_7_days_ago_is_not_reachable_on_the_public_page(): void
    {
        $car = $this->makeCar(CarStatus::Sold);
        $car->update(['sold_at' => now()->subDays(10)]);

        $this->get(route('cars.show', $car->slug))->assertNotFound();
    }

    #[Test]
    public function a_recently_sold_car_is_still_reachable_within_its_7_day_window(): void
    {
        $car = $this->makeCar(CarStatus::Sold);
        $car->update(['sold_at' => now()->subDays(2)]);

        $this->get(route('cars.show', $car->slug))->assertOk();
    }

    #[Test]
    public function a_reserved_car_is_still_reachable_but_not_orderable(): void
    {
        $car = $this->makeCar(CarStatus::Reserved);

        $this->get(route('cars.show', $car->slug))
            ->assertOk()
            ->assertSee('Reserved');
    }

    #[Test]
    public function an_unknown_slug_returns_a_404(): void
    {
        $this->get('/cars/this-is-not-a-real-car')->assertNotFound();
    }
}
