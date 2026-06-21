<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Pages;

use App\Filament\Resources\Cars\CarResource;
use App\Services\CarService;
use Filament\Resources\Pages\CreateRecord;

class CreateCar extends CreateRecord
{
    protected static string $resource = CarResource::class;

    /** Holds the uploaded photo paths between mutateFormDataBeforeCreate() and afterCreate(). */
    protected array $imagePaths = [];

    /**
     * Pulls the transient image_paths field off the form data so it's never passed to Car::create().
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->imagePaths = $data['image_paths'] ?? [];
        unset($data['image_paths']);

        return $data;
    }

    /**
     * Turns the uploaded photo paths into CarImage rows now that the car has an id.
     */
    protected function afterCreate(): void
    {
        app(CarService::class)->syncImages($this->record, $this->imagePaths);
    }
}
