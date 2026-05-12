<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Pages;

use App\Filament\Resources\Cars\CarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCar extends CreateRecord
{
    protected static string $resource = CarResource::class;

    protected function afterCreate(): void
    {
        $this->syncImages();
    }

    private function syncImages(): void
    {
        $paths = $this->data['image_paths'] ?? [];

        $this->record->images()->delete();

        foreach (array_values($paths) as $order => $path) {
            $this->record->images()->create([
                'path'       => $path,
                'sort_order' => $order,
            ]);
        }
    }
}
