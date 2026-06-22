<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\PaymentProofUploaded;
use App\Notifications\PaymentProofReceivedNotification;

class SendPaymentProofReceivedNotification
{
    public function handle(PaymentProofUploaded $event): void
    {
        $event->order->user->notify(new PaymentProofReceivedNotification($event->order));
    }
}
