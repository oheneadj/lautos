<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Enums\CarStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'make_id' => Make::inRandomOrder()->first()?->id,
            'car_model_id' => CarModel::inRandomOrder()->first()?->id,
            'year' => $this->faker->year(),
            'engine_capacity' => $this->faker->randomElement(['1.5L', '2.0L', '2.5L', '3.0L', '4.0L']),
            'transmission' => $this->faker->randomElement(['Automatic', 'Manual']),
            'fuel_type' => $this->faker->randomElement(['Petrol', 'Diesel', 'Hybrid', 'Electric']),
            'mileage' => $this->faker->numberBetween(0, 150000),
            'colour' => $this->faker->safeColorName(),
            'country_of_origin' => $this->faker->country(),
            'price_usd_cents' => $this->faker->numberBetween(500000, 5000000), // $5,000 to $50,000
            'shipping_cost_usd_cents' => $this->faker->numberBetween(100000, 500000), // $1,000 to $5,000
            'special_features' => $this->faker->randomElements([
                'Sunroof', 'Leather Seats', 'Navigation System', 'Bluetooth', 'Reverse Camera', 
                'Parking Sensors', 'Heated Seats', 'Alloy Wheels', 'Cruise Control'
            ], 3),
            'status' => $this->faker->randomElement(CarStatus::cases()),
            'sold_at' => null,
        ];
    }

    /**
     * Indicate that the car is sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::Sold,
            'sold_at' => now(),
        ]);
    }
}
