<?php

/**
 * @author Ohene Adjei
 */

namespace App\Enums;

/**
 * Defines whether a customer's uploaded payment proof is still awaiting
 * review, or has been accepted/rejected by an admin.
 */
enum PaymentProofStatus: string
{
    case Pending  = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    /** Returns a human-readable label for display. */
    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Pending',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
        };
    }

    /** Returns the Filament colour for the status badge. */
    public function colour(): string
    {
        return match ($this) {
            self::Pending  => 'warning',
            self::Accepted => 'success',
            self::Rejected => 'danger',
        };
    }
}
