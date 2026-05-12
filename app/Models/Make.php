<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Make extends Model
{
    protected $fillable = [
        'name',
        'icon_path',
    ];

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class)->orderBy('name');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
