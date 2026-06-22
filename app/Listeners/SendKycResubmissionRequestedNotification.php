<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\KycResubmissionRequested;
use App\Notifications\KycResubmissionRequestedNotification;

class SendKycResubmissionRequestedNotification
{
    public function handle(KycResubmissionRequested $event): void
    {
        $event->customer->notify(new KycResubmissionRequestedNotification($event->reason));
    }
}
