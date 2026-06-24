<?php

/**
 * @author Ohene Adjei
 */

namespace App\Channels;

/**
 * Simple value object that holds the SMS body text for a GiantSMS notification.
 */
class GiantSmsMessage
{
    public function __construct(public string $content)
    {
    }
}
