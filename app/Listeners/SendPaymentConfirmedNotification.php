<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Notifications\PaymentConfirmedNotification;

class SendPaymentConfirmedNotification
{
    public function handle(PaymentConfirmed $event): void
    {
        $event->order->user->notify(new PaymentConfirmedNotification($event->order));
    }
}
