<?php

namespace Database\Factories;

use App\Enums\CarBodyType;
use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Car>
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
            'year' => fake()->year(),
            'engine_capacity' => fake()->randomElement(['1.5L', '2.0L', '2.5L', '3.0L', '4.0L']),
            'transmission' => fake()->randomElement(Car::TRANSMISSIONS),
            'fuel_type' => fake()->randomElement(Car::FUEL_TYPES),
            'mileage' => fake()->numberBetween(0, 150000),
            'colour' => fake()->safeColorName(),
            'country_of_origin' => fake()->randomElement(Car::COUNTRIES_OF_ORIGIN),
            // Body type is just descriptive metadata, not a lockable status like
            // CarStatus, so a random pick here is safe and gives the catalogue/
            // homepage category tabs something realistic to show in dev.
            'body_type' => fake()->randomElement(CarBodyType::cases()),
            'price_usd_cents' => fake()->numberBetween(500000, 5000000), // $5,000 to $50,000
            'shipping_cost_usd_cents' => fake()->numberBetween(100000, 500000), // $1,000 to $5,000
            'special_features' => fake()->randomElements([
                'Sunroof', 'Leather Seats', 'Navigation System', 'Bluetooth', 'Reverse Camera',
                'Parking Sensors', 'Heated Seats', 'Alloy Wheels', 'Cruise Control',
            ], 3),
            // I default every car to Available — Reserved/Sold only mean something when
            // there's a real Order behind them (see OrderService::confirmPayment() and
            // Car::markSold()), so a factory can't fake those statuses on its own without
            // creating cars that look locked but have nothing reserving them.
            'status' => CarStatus::Available,
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

    /**
     * Indicate that the car is reserved. Opt-in only, like sold() — real
     * Reserved cars always have a PaymentConfirmed+ order behind them, so
     * callers needing a realistic scenario should pair this with an actual
     * Order via OrderService rather than relying on this alone.
     */
    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::Reserved,
        ]);
    }
}
