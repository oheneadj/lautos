<?php

namespace Database\Factories;

use App\Enums\SmsLogStatus;
use App\Models\SmsLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SmsLog>
 */
class SmsLogFactory extends Factory
{
    protected $model = SmsLog::class;

    public function definition(): array
    {
        return [
            'phone' => $this->faker->numerify('02########'),
            'message' => $this->faker->sentence(),
            'status' => SmsLogStatus::Sent,
            'context' => null,
            'http_status' => 200,
            'response_body' => null,
            'error_message' => null,
        ];
    }
}
