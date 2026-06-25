<?php

/**
 * Handles operations on a car model that are more than a plain CRUD save.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Models\CarModel;

class CarModelService
{
    /**
     * Replaces a model's trims with the given list of names — anything not
     * in the list gets removed, anything new gets created, and existing
     * trims are left untouched so their id (and any cars pointing at it)
     * survive the sync.
     *
     * @param  array<int, string>  $names
     */
    public function syncTrims(CarModel $model, array $names): void
    {
        $model->trims()->whereNotIn('name', $names)->delete();

        foreach ($names as $name) {
            $model->trims()->firstOrCreate(['name' => $name]);
        }
    }
}
