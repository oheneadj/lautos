<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\PaymentRejected;
use App\Notifications\PaymentRejectedNotification;

class SendPaymentRejectedNotification
{
    public function handle(PaymentRejected $event): void
    {
        $event->order->user->notify(new PaymentRejectedNotification($event->order, $event->reason));
    }
}
