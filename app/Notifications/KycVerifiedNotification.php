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

class KycVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
            ->subject('Your KYC Has Been Verified')
            ->greeting("Hi {$notifiable->name},")
            ->line('Good news — your identity documents have been verified.')
            ->line('You can now place car orders and have them delivered to you.')
            ->action('View Your Profile', route('dashboard.profile'))
            ->line('Contact us if you have any questions.');
    }

    public function toGiantSms(object $notifiable): GiantSmsMessage
    {
        return new GiantSmsMessage(
            "Hi {$notifiable->name}, your KYC documents have been verified. You can now place car orders."
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'KYC Verified',
            'message' => 'Your identity documents have been verified. You can now place car orders.',
            'icon' => 'check',
            'action_url' => route('dashboard.profile'),
            'action_text' => 'View Profile',
        ];
    }
}
