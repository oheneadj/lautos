<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycResubmissionRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $reason)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // I only send mail for now — SMS (Arkesel) is wired up in Epic 21.
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Action Needed — Please Resubmit Your KYC Documents')
            ->greeting("Hi {$notifiable->name},")
            ->line('We need you to resubmit your identity documents before we can verify your account.')
            ->line("Reason: {$this->reason}")
            ->action('Update Your Documents', route('dashboard.profile'))
            ->line('Contact us if you have any questions.');
    }
}
