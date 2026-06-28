<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\KycVerified;
use App\Notifications\KycVerifiedNotification;

class SendKycVerifiedNotification
{
    public function handle(KycVerified $event): void
    {
        $event->customer->notify(new KycVerifiedNotification);
    }
}
