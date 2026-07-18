<?php

namespace Database\Seeders;

use App\Enums\CarBodyType;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/scraped_cars.json');

        if (File::exists($jsonPath)) {
            $carsData = json_decode(File::get($jsonPath), true);

            foreach ($carsData as $data) {
                // Clean up scraped anomalies (due to hyphenated URLs)
                if ($data['make'] === 'Mercedes' && $data['model'] === 'Benz') {
                    $data['make'] = 'Mercedes-Benz';
                    $data['model'] = 'C-Class'; // Default fallback model
                }
                if ($data['make'] === 'Honda' && $data['model'] === 'Cr') {
                    $data['model'] = 'CR-V';
                }
                if ($data['make'] === 'Ford' && $data['model'] === 'F') {
                    $data['model'] = 'F-150';
                }

                // Ensure Make exists
                $make = Make::firstOrCreate(
                    ['name' => $data['make']]
                );

                // Ensure CarModel exists
                $carModel = CarModel::firstOrCreate(
                    [
                        'make_id' => $make->id,
                        'name' => $data['model'],
                    ]
                );

                // Guess body type based on model name
                $modelLower = strtolower($data['model']);
                $bodyType = match (true) {
                    str_contains($modelLower, 'tundra') || str_contains($modelLower, 'tacoma') || str_contains($modelLower, 'f150') || str_contains($modelLower, 'f-150') || str_contains($modelLower, 'f 150') => CarBodyType::PickupTruck,
                    str_contains($modelLower, 'sienna') || str_contains($modelLower, 'odyssey') => CarBodyType::VanMinivan,
                    str_contains($modelLower, 'rav4') || str_contains($modelLower, 'highlander') || str_contains($modelLower, 'tucson') || str_contains($modelLower, 'cr-v') || str_contains($modelLower, 'cr v') || str_contains($modelLower, 'edge') || str_contains($modelLower, 'armada') => CarBodyType::Suv,
                    str_contains($modelLower, 'yaris') || str_contains($modelLower, 'matrix') || str_contains($modelLower, 'juke') => CarBodyType::Hatchback,
                    str_contains($modelLower, 'mustang') => CarBodyType::Coupe,
                    default => CarBodyType::Sedan,
                };

                // Create the car, overriding factory defaults with our realistic data
                $car = Car::factory()->create([
                    'make_id' => $make->id,
                    'car_model_id' => $carModel->id,
                    'year' => $data['year'],
                    'price_usd_cents' => $data['price_usd_cents'],
                    'mileage' => $data['mileage'],
                    'body_type' => $bodyType,
                ]);

                // Attach the scraped images
                if (isset($data['image_paths']) && is_array($data['image_paths'])) {
                    foreach ($data['image_paths'] as $index => $path) {
                        $car->images()->create([
                            'path' => $path,
                            'sort_order' => $index + 1,
                        ]);
                    }
                } elseif (isset($data['image_path'])) {
                    // Fallback for old data structure
                    $car->images()->create([
                        'path' => $data['image_path'],
                        'sort_order' => 1,
                    ]);
                }
            }
        } else {
            Car::factory()->count(50)->create();
        }
    }
}
