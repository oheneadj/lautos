<?php

namespace Tests\Unit\Services;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Services\CarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests syncing a car's photo set from an ordered list of storage paths.
 */
class CarServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(): Car
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
    }

    #[Test]
    public function it_creates_an_image_row_per_path_in_order(): void
    {
        $car = $this->makeCar();

        (new CarService())->syncImages($car, ['cars/a.jpg', 'cars/b.jpg', 'cars/c.jpg']);

        $images = $car->images()->orderBy('sort_order')->get();

        $this->assertCount(3, $images);
        $this->assertSame('cars/a.jpg', $images[0]->path);
        $this->assertSame(0, $images[0]->sort_order);
        $this->assertSame('cars/c.jpg', $images[2]->path);
        $this->assertSame(2, $images[2]->sort_order);
    }

    #[Test]
    public function it_replaces_the_existing_set_rather_than_appending(): void
    {
        $car = $this->makeCar();

        (new CarService())->syncImages($car, ['cars/old-1.jpg', 'cars/old-2.jpg']);
        (new CarService())->syncImages($car, ['cars/new-1.jpg']);

        $images = $car->images;

        $this->assertCount(1, $images);
        $this->assertSame('cars/new-1.jpg', $images->first()->path);
    }

    #[Test]
    public function reordering_the_paths_updates_sort_order_accordingly(): void
    {
        $car = $this->makeCar();

        (new CarService())->syncImages($car, ['cars/first.jpg', 'cars/second.jpg']);
        (new CarService())->syncImages($car, ['cars/second.jpg', 'cars/first.jpg']);

        $images = $car->images()->orderBy('sort_order')->get();

        $this->assertSame('cars/second.jpg', $images[0]->path);
        $this->assertSame('cars/first.jpg', $images[1]->path);
    }
}
