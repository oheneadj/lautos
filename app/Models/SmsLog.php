<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\SmsLogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Records a single GiantSMS API call (request + response) for troubleshooting.
 */
class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'message',
        'status',
        'context',
        'http_status',
        'response_body',
        'error_message',
    ];

    protected $casts = [
        'status' => SmsLogStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            if (empty($log->uuid)) {
                $log->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * I route by uuid so integer ids never appear in the admin URL.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
