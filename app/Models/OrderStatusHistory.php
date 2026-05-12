<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    // I disable updated_at — history rows are append-only.
    public const UPDATED_AT = null;

    protected $fillable = [
        'order_id',
        'status',
        'changed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status'     => OrderStatus::class,
            'created_at' => 'datetime',
        ];
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
