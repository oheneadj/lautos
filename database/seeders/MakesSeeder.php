<?php

/**
 * Seeds the most common car makes sold in Ghana via import dealers.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use App\Models\Make;
use Illuminate\Database\Seeder;

class MakesSeeder extends Seeder
{
    public function run(): void
    {
        $makes = [
            'Toyota',
            'Honda',
            'Nissan',
            'Mazda',
            'Mitsubishi',
            'Subaru',
            'Suzuki',
            'Lexus',
            'Hyundai',
            'Kia',
            'Daewoo',
            'Ssangyong',
            'BMW',
            'Mercedes-Benz',
            'Volkswagen',
            'Audi',
            'Ford',
            'Chevrolet',
            'Land Rover',
            'Jeep',
            'Peugeot',
            'Renault',
            'Volvo',
            'Isuzu',
        ];

        foreach ($makes as $name) {
            Make::firstOrCreate(['name' => $name]);
        }
    }
}
