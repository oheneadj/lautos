<?php

namespace Tests\Unit\Models;

use App\Filament\Resources\Cars\CarResource;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests that admin links generated from a Car model never expose the raw integer id.
 */
class CarRouteKeyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function car_route_key_is_the_uuid_not_the_integer_id(): void
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        $this->assertSame('uuid', $car->getRouteKeyName());
        $this->assertSame($car->uuid, $car->getRouteKey());
    }

    #[Test]
    public function the_filament_edit_link_uses_the_uuid_not_the_integer_id(): void
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        $url = CarResource::getUrl('edit', ['record' => $car]);

        $this->assertStringContainsString("/admin/cars/{$car->uuid}/edit", $url);
        $this->assertStringNotContainsString("/admin/cars/{$car->id}/edit", $url);
    }
}
