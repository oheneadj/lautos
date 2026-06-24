<?php

namespace Database\Factories;

use App\Enums\ReviewStatus;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'rating' => $this->faker->numberBetween(3, 5),
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->paragraph(),
            'status' => ReviewStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(['status' => ReviewStatus::Approved, 'approved_at' => now()]);
    }

    public function rejected(): static
    {
        return $this->state(['status' => ReviewStatus::Rejected]);
    }
}
