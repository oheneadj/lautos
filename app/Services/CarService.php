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
    public function __construct(private ImageOptimizer $imageOptimizer)
    {
    }

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
            // I re-encode to WebP and cap the width here rather than in the upload field
            // itself, so this applies no matter how the path got onto the car.
            $optimizedPath = $this->imageOptimizer->optimize('public', $path, maxWidth: 1200);

            $car->images()->create([
                'path' => $optimizedPath,
                'sort_order' => $index,
            ]);
        }
    }
}
