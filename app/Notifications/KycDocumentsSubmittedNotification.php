<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycDocumentsSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $customer)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // This goes to the admin's business email, not a customer with a
        // phone number, so there's no SMS channel to add here.
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New KYC Documents Submitted')
            ->greeting('KYC documents need review')
            ->line("{$this->customer->name} ({$this->customer->email}) has submitted new KYC documents for review.")
            ->action('Review in Admin', url('/admin'));
    }
}
