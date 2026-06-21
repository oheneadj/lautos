<?php

/**
 * Handles car business logic that doesn't belong in the admin pages or model.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Models\Car;

class CarService
{
    /**
     * Replaces a car's photo set with the given storage paths, preserving the chosen order.
     *
     * I do a full delete-and-recreate rather than diffing, since CarImage rows carry
     * no other state worth preserving beyond path and order.
     */
    public function syncImages(Car $car, array $paths): void
    {
        $car->images()->delete();

        foreach (array_values($paths) as $index => $path) {
            $car->images()->create([
                'path' => $path,
                'sort_order' => $index,
            ]);
        }
    }
}
