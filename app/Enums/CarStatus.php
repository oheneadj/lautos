<?php

/**
 * Defines the possible availability states of a car listing.
 *
 * @author Ohene Adjei
 */

namespace App\Enums;

enum CarStatus: string
{
    case Available = 'available';
    case Reserved  = 'reserved';
    case Sold      = 'sold';

    /** Returns a human-readable label for display. */
    public function label(): string
    {
        return match($this) {
            self::Available => 'Available',
            self::Reserved  => 'Reserved',
            self::Sold      => 'Sold',
        };
    }

    /** Returns the Tailwind colour class for the status badge. */
    public function colour(): string
    {
        return match($this) {
            self::Available => 'success',
            self::Reserved  => 'warning',
            self::Sold      => 'danger',
        };
    }
}
