<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends Model
{
    protected $fillable = [
        'make_id',
        'name',
    ];

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class);
    }

    public function trims(): HasMany
    {
        return $this->hasMany(CarTrim::class)->orderBy('name');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
