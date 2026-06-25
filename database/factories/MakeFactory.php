<?php

namespace Database\Factories;

use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Make>
 */
class MakeFactory extends Factory
{
    protected $model = Make::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
        ];
    }
}
