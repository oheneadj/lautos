<?php

namespace Tests\Unit\Jobs;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the cars:archive-sold command (US-08).
 */
class ArchiveSoldCarsTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(array $attributes = []): Car
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create(array_merge([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
        ], $attributes));
    }

    #[Test]
    public function it_archives_a_car_sold_more_than_7_days_ago(): void
    {
        $car = $this->makeCar(['status' => CarStatus::Sold, 'sold_at' => now()->subDays(8)]);

        $this->artisan('cars:archive-sold')->assertSuccessful();

        $this->assertSoftDeleted($car);
    }

    #[Test]
    public function it_leaves_a_car_sold_within_the_last_7_days_alone(): void
    {
        $car = $this->makeCar(['status' => CarStatus::Sold, 'sold_at' => now()->subDays(3)]);

        $this->artisan('cars:archive-sold')->assertSuccessful();

        $this->assertNotSoftDeleted($car);
    }

    #[Test]
    public function it_leaves_available_cars_alone_regardless_of_age(): void
    {
        $car = $this->makeCar(['status' => CarStatus::Available, 'sold_at' => null]);

        $this->artisan('cars:archive-sold')->assertSuccessful();

        $this->assertNotSoftDeleted($car);
    }
}
