<?php

/**
 * A customer's review of a delivered order — one per order, moderated by
 * an admin before it can appear on the public site.
 *
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Review extends Model
{
    use HasFactory, LogsActivity;

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
        'order_id',
        'rating',
        'title',
        'body',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReviewStatus::class,
            'rating' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * I route by uuid so the integer id never appears in admin links.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $review) {
            if (empty($review->uuid)) {
                $review->uuid = (string) Str::uuid();
            }
        });
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * I use a local scope so the homepage carousel and admin filters never
     * have to repeat this status check by hand.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Approved);
    }
}
