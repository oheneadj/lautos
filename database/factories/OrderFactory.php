<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Car;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'car_id' => Car::factory(),
            'status' => OrderStatus::PendingPayment,
            'car_year' => $this->faker->numberBetween(2010, 2024),
            'car_make_name' => $this->faker->word(),
            'car_model_name' => $this->faker->word(),
            'price_usd_cents' => $this->faker->numberBetween(500000, 5000000),
            'shipping_cost_usd_cents' => $this->faker->numberBetween(100000, 500000),
        ];
    }

    /**
     * I snapshot the given car's identity onto the order, matching what
     * OrderService::createOrder() does for real orders — use this when a
     * test asserts the order displays a specific car's details.
     */
    public function forCar(Car $car): static
    {
        return $this->state([
            'car_id' => $car->id,
            'car_year' => $car->year,
            'car_make_name' => $car->make?->name,
            'car_model_name' => $car->carModel?->name,
            'car_thumbnail_path' => $car->images->first()?->path,
        ]);
    }
}
