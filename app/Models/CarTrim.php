<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CarTrim extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_model_id',
        'name',
    ];

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
