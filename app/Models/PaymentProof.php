<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

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
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
