<?php

/**
 * Seeds popular models for each make — focused on Japanese/Korean imports common in Ghana.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Database\Seeder;

class CarModelsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Toyota'        => ['Corolla', 'Camry', 'RAV4', 'Hilux', 'Land Cruiser', 'Prado', 'Vitz', 'Yaris', 'Fortuner', 'Highlander', 'Prius', 'Avensis', 'Wish', 'Noah', 'Voxy', 'Alphard', 'Hiace', 'Rush'],
            'Honda'         => ['Civic', 'Accord', 'CR-V', 'HR-V', 'Fit', 'Jazz', 'Pilot', 'Odyssey', 'Stream', 'Stepwgn', 'Freed', 'Vezel'],
            'Nissan'        => ['Sentra', 'Altima', 'X-Trail', 'Juke', 'Qashqai', 'Murano', 'Patrol', 'Navara', 'Note', 'Tiida', 'Bluebird', 'Primera'],
            'Mazda'         => ['Mazda2', 'Mazda3', 'Mazda6', 'CX-3', 'CX-5', 'CX-7', 'CX-9', 'Demio', 'Atenza', 'Axela'],
            'Mitsubishi'    => ['Lancer', 'Outlander', 'Pajero', 'Eclipse Cross', 'ASX', 'Galant', 'Colt', 'L200'],
            'Subaru'        => ['Impreza', 'Legacy', 'Forester', 'Outback', 'XV', 'BRZ', 'Exiga'],
            'Suzuki'        => ['Swift', 'Vitara', 'Grand Vitara', 'SX4', 'Alto', 'Jimny', 'Baleno'],
            'Lexus'         => ['IS', 'ES', 'GS', 'LS', 'RX', 'NX', 'GX', 'LX', 'UX'],
            'Hyundai'       => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Accent', 'i10', 'i20', 'i30', 'Creta', 'Kona'],
            'Kia'           => ['Rio', 'Cerato', 'Optima', 'Sportage', 'Sorento', 'Picanto', 'Stinger', 'Seltos'],
            'BMW'           => ['3 Series', '5 Series', '7 Series', 'X1', 'X3', 'X5', 'X6', '1 Series', '4 Series'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE', 'A-Class', 'CLA', 'GLK'],
            'Volkswagen'    => ['Golf', 'Passat', 'Tiguan', 'Polo', 'Touareg', 'Jetta', 'Phaeton'],
            'Audi'          => ['A3', 'A4', 'A6', 'Q3', 'Q5', 'Q7', 'A1', 'A8'],
            'Ford'          => ['Focus', 'Mondeo', 'Fiesta', 'Escape', 'Explorer', 'Ranger', 'Everest', 'Fusion'],
            'Chevrolet'     => ['Cruze', 'Malibu', 'Captiva', 'Trailblazer', 'Aveo', 'Spark', 'Tahoe'],
            'Land Rover'    => ['Range Rover', 'Discovery', 'Freelander', 'Defender', 'Range Rover Sport', 'Discovery Sport'],
            'Jeep'          => ['Wrangler', 'Cherokee', 'Grand Cherokee', 'Compass', 'Renegade'],
            'Peugeot'       => ['206', '207', '208', '307', '308', '407', '508', '3008', '5008', '2008'],
            'Renault'       => ['Clio', 'Megane', 'Scenic', 'Laguna', 'Duster', 'Koleos', 'Kadjar'],
            'Volvo'         => ['S40', 'S60', 'S80', 'XC40', 'XC60', 'XC90', 'V40', 'V60'],
            'Isuzu'         => ['D-Max', 'MU-X', 'Trooper', 'Rodeo'],
            'Daewoo'        => ['Matiz', 'Lanos', 'Nubira', 'Leganza', 'Kalos'],
            'Ssangyong'     => ['Rexton', 'Korando', 'Tivoli', 'Musso', 'Rodius'],
        ];

        foreach ($data as $makeName => $models) {
            $make = Make::where('name', $makeName)->first();

            if (! $make) {
                continue;
            }

            foreach ($models as $modelName) {
                CarModel::firstOrCreate([
                    'make_id' => $make->id,
                    'name'    => $modelName,
                ]);
            }
        }
    }
}
