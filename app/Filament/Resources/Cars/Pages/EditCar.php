<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Pages;

use App\Filament\Resources\Cars\CarResource;
use App\Services\CarService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCar extends EditRecord
{
    protected static string $resource = CarResource::class;

    /** Holds the uploaded photo paths between mutateFormDataBeforeSave() and afterSave(). */
    protected array $imagePaths = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Loads the car's existing photo paths into the multi-upload field, in their saved order.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image_paths'] = $this->record->images->pluck('path')->all();

        return $data;
    }

    /**
     * Pulls the transient image_paths field off the form data so it's never passed to Car::update().
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->imagePaths = $data['image_paths'] ?? [];
        unset($data['image_paths']);

        return $data;
    }

    /**
     * Re-syncs the car's CarImage rows from whatever photo paths are left after editing.
     */
    protected function afterSave(): void
    {
        app(CarService::class)->syncImages($this->record, $this->imagePaths);
    }
}
