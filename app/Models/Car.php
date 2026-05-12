<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\CarStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'make_id',
        'car_model_id',
        'car_trim_id',
        'year',
        'engine_capacity',
        'transmission',
        'fuel_type',
        'mileage',
        'colour',
        'country_of_origin',
        'price_usd_cents',
        'shipping_cost_usd_cents',
        'special_features',
        'status',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'special_features' => 'array',
            'status'           => CarStatus::class,
            'sold_at'          => 'datetime',
            'year'             => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $car) {
            if (empty($car->uuid)) {
                $car->uuid = (string) Str::uuid();
            }
        });
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getPriceUsdAttribute(): float
    {
        return $this->price_usd_cents / 100;
    }

    public function getShippingCostUsdAttribute(): float
    {
        return $this->shipping_cost_usd_cents / 100;
    }

    public function getTotalUsdCentsAttribute(): int
    {
        return $this->price_usd_cents + $this->shipping_cost_usd_cents;
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function carTrim(): BelongsTo
    {
        return $this->belongsTo(CarTrim::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
