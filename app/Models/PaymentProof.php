<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\PaymentProofStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PaymentProof extends Model
{
    protected $fillable = [
        'order_id',
        'file_path',
        'note',
        'status',
    ];

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
