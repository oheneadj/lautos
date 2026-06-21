<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Car;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
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
            'price_usd_cents' => $this->faker->numberBetween(500000, 5000000),
            'shipping_cost_usd_cents' => $this->faker->numberBetween(100000, 500000),
        ];
    }
}
