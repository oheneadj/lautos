<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\OrderStageUpdated;
use App\Notifications\ReviewRequestNotification;

class SendReviewRequestNotification
{
    /**
     * I only prompt for a review the moment an order reaches Delivered —
     * that's the one stage where the purchase is genuinely complete.
     */
    public function handle(OrderStageUpdated $event): void
    {
        if ($event->order->status !== OrderStatus::Delivered) {
            return;
        }

        $event->order->user->notify(new ReviewRequestNotification($event->order));
    }
}
