<?php

namespace Database\Factories;

use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarModel>
 */
class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    public function definition(): array
    {
        return [
            'make_id' => Make::factory(),
            'name' => $this->faker->unique()->word(),
        ];
    }
}
