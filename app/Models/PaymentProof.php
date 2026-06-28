<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\PaymentProofStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentProof extends Model
{
    protected $fillable = [
        'order_id',
        'file_path',
        'note',
        'status',
    ];

    /**
     * I route by uuid so the integer id never appears in the signed
     * payment-proof viewing URL.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $proof) {
            if (empty($proof->uuid)) {
                $proof->uuid = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => PaymentProofStatus::class,
        ];
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
