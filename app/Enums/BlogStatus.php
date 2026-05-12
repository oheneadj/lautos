<?php

/**
 * Defines the publication states for a blog post.
 *
 * @author Ohene Adjei
 */

namespace App\Enums;

enum BlogStatus: string
{
    case Draft     = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';

    /** Returns a human-readable label for display. */
    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Published => 'Published',
        };
    }

    /** Returns the Filament colour for the status badge. */
    public function colour(): string
    {
        return match($this) {
            self::Draft     => 'warning',
            self::Scheduled => 'info',
            self::Published => 'success',
        };
    }
}
