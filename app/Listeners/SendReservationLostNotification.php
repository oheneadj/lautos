<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\ReservationLost;
use App\Notifications\ReservationLostNotification;

class SendReservationLostNotification
{
    public function handle(ReservationLost $event): void
    {
        $event->order->user->notify(new ReservationLostNotification($event->order));
    }
}
