<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use App\Channels\GiantSmsMessage;
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
        $channels = ['mail', 'database'];

        if (! empty($notifiable->phone)) {
            $channels[] = 'giantsms';
        }

        return $channels;
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

    public function toGiantSms(object $notifiable): GiantSmsMessage
    {
        $url = route('dashboard.profile');

        return new GiantSmsMessage(
            "Hi {$notifiable->name}, please resubmit your KYC documents. Reason: {$this->reason}. Update at {$url}"
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'KYC Resubmission Required',
            'message' => "We need you to resubmit your identity documents. Reason: {$this->reason}",
            'icon' => 'document',
            'action_url' => route('dashboard.profile'),
            'action_text' => 'Update Documents',
        ];
    }
}
