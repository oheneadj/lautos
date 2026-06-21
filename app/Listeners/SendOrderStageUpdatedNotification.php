<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\OrderStageUpdated;
use App\Notifications\OrderStageUpdatedNotification;

class SendOrderStageUpdatedNotification
{
    public function handle(OrderStageUpdated $event): void
    {
        $event->order->user->notify(new OrderStageUpdatedNotification($event->order));
    }
}
