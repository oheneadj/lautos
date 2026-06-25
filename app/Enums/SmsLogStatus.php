<?php

/**
 * Defines the outcome of a single GiantSMS API call, recorded on each row
 * of sms_logs for troubleshooting delivery issues.
 *
 * @author Ohene Adjei
 */

namespace App\Enums;

enum SmsLogStatus: string
{
    case Sent   = 'sent';
    case Failed = 'failed';

    /** Returns a human-readable label for display. */
    public function label(): string
    {
        return match($this) {
            self::Sent   => 'Sent',
            self::Failed => 'Failed',
        };
    }

    /** Returns the Filament colour for the status badge. */
    public function colour(): string
    {
        return match($this) {
            self::Sent   => 'success',
            self::Failed => 'danger',
        };
    }
}
