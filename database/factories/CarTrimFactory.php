<?php

namespace Database\Factories;

use App\Models\CarModel;
use App\Models\CarTrim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarTrim>
 */
class CarTrimFactory extends Factory
{
    protected $model = CarTrim::class;

    public function definition(): array
    {
        return [
            'car_model_id' => CarModel::factory(),
            'name' => $this->faker->unique()->word(),
        ];
    }
}
