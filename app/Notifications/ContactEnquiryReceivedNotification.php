<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use App\Models\ContactEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactEnquiryReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ContactEnquiry $enquiry)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Contact Enquiry: {$this->enquiry->subject}")
            ->greeting('New enquiry from the website')
            ->line("From: {$this->enquiry->name} ({$this->enquiry->email})")
            ->line($this->enquiry->phone ? "Phone: {$this->enquiry->phone}" : 'Phone: not provided')
            ->line("Subject: {$this->enquiry->subject}")
            ->line($this->enquiry->message);
    }
}
