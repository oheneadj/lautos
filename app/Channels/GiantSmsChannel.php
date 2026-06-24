<?php

/**
 * @author Ohene Adjei
 */

namespace App\Channels;

use App\Jobs\SendGiantSms;
use Illuminate\Notifications\Notification;

/**
 * Custom Laravel notification channel for sending SMS via the GiantSMS Ghana API.
 *
 * Notifications opt in by adding 'giantsms' to their via() array and
 * implementing a toGiantSms($notifiable) method that returns a GiantSmsMessage.
 */
class GiantSmsChannel
{
    /**
     * Queues the actual API call as its own job rather than calling the
     * gateway here directly — that way a slow or down gateway gets its own
     * retry/backoff (see SendGiantSms) instead of affecting the mail and
     * database channels in this same notification.
     *
     * @param  mixed        $notifiable
     * @param  Notification $notification
     * @return void
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $apiKey = config('services.giantsms.api_key');

        // I skip silently when no API key is set — local dev shouldn't need a live SMS gateway.
        if (empty($apiKey)) {
            return;
        }

        $phone = $notifiable->routeNotificationFor('giantsms', $notification);

        // I skip if the user has no phone number — can't send SMS to nothing.
        if (empty($phone)) {
            return;
        }

        /** @var GiantSmsMessage $message */
        $message = $notification->toGiantSms($notifiable);

        $content = $message instanceof GiantSmsMessage
            ? $message->content
            : (string) $message;

        SendGiantSms::dispatch($phone, $content);
    }
}
