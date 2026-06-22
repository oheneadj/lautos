<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\KycDocumentsSubmitted;
use App\Models\Setting;
use App\Notifications\KycDocumentsSubmittedNotification;
use Illuminate\Support\Facades\Notification;

class SendKycDocumentsSubmittedNotification
{
    public function handle(KycDocumentsSubmitted $event): void
    {
        $adminEmail = Setting::get('contact_email');

        if (empty($adminEmail)) {
            return;
        }

        Notification::route('mail', $adminEmail)
            ->notify(new KycDocumentsSubmittedNotification($event->user));
    }
}
