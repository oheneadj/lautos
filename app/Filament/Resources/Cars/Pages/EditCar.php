<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Pages;

use App\Filament\Resources\Cars\CarResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCar extends EditRecord
{
    protected static string $resource = CarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    // I pre-populate the uploader with the car's existing image paths on load.
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image_paths'] = $this->record->images()
            ->orderBy('sort_order')
            ->pluck('path')
            ->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncImages();
    }

    private function syncImages(): void
    {
        $paths = $this->data['image_paths'] ?? [];

        // I delete and re-insert so reordering and removals are handled in one pass.
        $this->record->images()->delete();

        foreach (array_values($paths) as $order => $path) {
            $this->record->images()->create([
                'path'       => $path,
                'sort_order' => $order,
            ]);
        }
    }
}
