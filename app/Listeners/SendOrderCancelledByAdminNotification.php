<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\OrderCancelledByAdmin;
use App\Notifications\OrderCancelledByAdminNotification;

class SendOrderCancelledByAdminNotification
{
    public function handle(OrderCancelledByAdmin $event): void
    {
        $event->order->user->notify(new OrderCancelledByAdminNotification($event->order, $event->reason));
    }
}
