<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\CarStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Car extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * I log only the fields an admin actually cares about seeing change —
     * not every internal/derived column.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status', 'price_usd_cents', 'shipping_cost_usd_cents',
                'mileage', 'colour', 'transmission', 'fuel_type', 'country_of_origin',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    // I keep these option lists here as the single source of truth — the admin form and the
    // public catalogue filters both read from these instead of duplicating the literal arrays.
    public const TRANSMISSIONS = ['Automatic', 'Manual'];
    public const FUEL_TYPES = ['Petrol', 'Diesel', 'Hybrid'];
    public const COUNTRIES_OF_ORIGIN = ['Japan', 'Korea', 'Europe', 'USA', 'Other'];

    protected $fillable = [
        'uuid',
        'slug',
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

    /**
     * I route by uuid everywhere Laravel generates a link from a model instance (e.g. Filament's
     * admin edit links) — the integer id is never exposed in a URL.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $car) {
            if (empty($car->uuid)) {
                $car->uuid = (string) Str::uuid();
            }

            if (empty($car->slug)) {
                $car->slug = static::uniqueSlug(Str::slug("{$car->year}-{$car->make->name}-{$car->carModel->name}"));
            }

            // I record sold_at here too in case a car is created already marked Sold.
            if ($car->status === CarStatus::Sold && empty($car->sold_at)) {
                $car->sold_at = now();
            }
        });

        // I keep sold_at in sync with status — set when a car becomes Sold, cleared otherwise —
        // so the 7-day auto-archive window (ArchiveSoldCars) is always measuring from the
        // most recent sale, not a stale timestamp left over from a previous status change.
        static::updating(function (self $car) {
            if (! $car->isDirty('status')) {
                return;
            }

            if ($car->status === CarStatus::Sold) {
                $car->sold_at = $car->sold_at ?? now();
            } else {
                $car->sold_at = null;
            }
        });
    }

    // I append an incrementing suffix (-2, -3 …) until the slug is unique, same pattern as BlogPost.
    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug   = $base;
        $suffix = 2;

        while (
            static::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
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

    public function getPriceGhsAttribute(): float
    {
        return $this->price_usd * $this->currentExchangeRate();
    }

    public function getShippingCostGhsAttribute(): float
    {
        return $this->shipping_cost_usd * $this->currentExchangeRate();
    }

    public function getTotalGhsAttribute(): float
    {
        return ($this->total_usd_cents / 100) * $this->currentExchangeRate();
    }

    /**
     * Builds a wa.me deeplink pre-filled with this car's details, or null if no number is configured.
     */
    public function getWhatsappEnquiryUrlAttribute(): ?string
    {
        $number = preg_replace('/\D/', '', (string) Setting::get('whatsapp_number', ''));

        if (! $number) {
            return null;
        }

        $message = "I am interested in the {$this->year} {$this->make->name} {$this->carModel->name} listed on Livingston Autos.";

        return 'https://wa.me/' . $number . '?text=' . urlencode($message);
    }

    /**
     * Reads the admin-configured USD to GHS exchange rate used for all price conversions.
     */
    public static function currentExchangeRate(): float
    {
        return (float) Setting::get('exchange_rate_usd_to_ghs', 15);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Limits the query to cars the public catalogue should show: Available and Reserved
     * cars always, plus Sold cars still inside their 7-day post-sale visibility window.
     */
    public function scopeVisibleOnCatalogue(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereIn('status', [CarStatus::Available, CarStatus::Reserved])
                ->orWhere(function (Builder $sold) {
                    $sold->where('status', CarStatus::Sold)
                        ->where('sold_at', '>=', now()->subDays(7));
                });
        });
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
