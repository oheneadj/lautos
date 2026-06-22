<?php

/**
 * @author Ohene Adjei
 */

namespace App\Listeners;

use App\Events\ContactEnquirySubmitted;
use App\Models\Setting;
use App\Notifications\ContactEnquiryReceivedNotification;
use Illuminate\Support\Facades\Notification;

class SendContactEnquiryNotification
{
    public function handle(ContactEnquirySubmitted $event): void
    {
        $adminEmail = Setting::get('contact_email');

        if (empty($adminEmail)) {
            return;
        }

        // I route to the configured business inbox directly rather than a specific
        // User record — Mr. Seth manages this address, not necessarily a single admin account.
        Notification::route('mail', $adminEmail)
            ->notify(new ContactEnquiryReceivedNotification($event->enquiry));
    }
}
