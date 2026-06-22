<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\PaymentProofUploaded;
use App\Models\Setting;
use App\Notifications\PaymentProofUploadedNotification;
use Illuminate\Support\Facades\Notification;

class SendPaymentProofUploadedNotification
{
    public function handle(PaymentProofUploaded $event): void
    {
        $adminEmail = Setting::get('contact_email');

        if (empty($adminEmail)) {
            return;
        }

        Notification::route('mail', $adminEmail)
            ->notify(new PaymentProofUploadedNotification($event->order));
    }
}
