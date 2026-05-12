<?php

/**
 * Defines the KYC verification states for a customer account.
 *
 * @author Ohene Adjei
 */

namespace App\Enums;

enum KycStatus: string
{
    case Pending           = 'pending';
    case Verified          = 'verified';
    case NeedsResubmission = 'needs_resubmission';

    /** Returns a human-readable label for display. */
    public function label(): string
    {
        return match($this) {
            self::Pending           => 'Pending Review',
            self::Verified          => 'Verified',
            self::NeedsResubmission => 'Needs Resubmission',
        };
    }

    /** Returns the Filament colour for the status badge. */
    public function colour(): string
    {
        return match($this) {
            self::Pending           => 'warning',
            self::Verified          => 'success',
            self::NeedsResubmission => 'danger',
        };
    }
}
