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
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Order extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'uuid',
        'user_id',
        'car_id',
        'status',
        'car_year',
        'car_make_name',
        'car_model_name',
        'car_thumbnail_path',
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
            'status' => OrderStatus::class,
            'estimated_arrival_date' => 'date',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * I route by uuid everywhere — admin links, customer dashboard links,
     * and Filament's resource URLs all need this, not just the explicit
     * {order:uuid} route bindings I write by hand.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
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

    /**
     * There's no separate sequential order number — I use the first 8
     * characters of the uuid as a short, human-quotable reference for
     * emails and support conversations.
     */
    public function getReferenceAttribute(): string
    {
        return 'LA-'.strtoupper(substr($this->uuid, 0, 8));
    }

    /**
     * There's no separate payment_status column — payment is just the first
     * three stages of the same pipeline. I derive a label here so the admin
     * table/filters can show it as its own concept without duplicating state.
     */
    public function getPaymentStatusAttribute(): string
    {
        return match ($this->status) {
            OrderStatus::PendingPayment => 'Pending',
            OrderStatus::PaymentUploaded => 'Uploaded',
            OrderStatus::Cancelled => 'Cancelled',
            default => 'Confirmed',
        };
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

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
