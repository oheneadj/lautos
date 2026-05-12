<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'car_id',
        'status',
        'price_usd_cents',
        'shipping_cost_usd_cents',
        'estimated_arrival_date',
        'tracking_number',
        'vessel_name',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'status'                => OrderStatus::class,
            'estimated_arrival_date'=> 'date',
            'delivered_at'          => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getTotalUsdCentsAttribute(): int
    {
        return $this->price_usd_cents + $this->shipping_cost_usd_cents;
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('created_at');
    }

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class)->latest();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(OrderNote::class)->latest();
    }
}
