<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use App\Channels\GiantSmsMessage;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
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
        $car = $this->order->car;

        return (new MailMessage)
            ->subject('How was your experience with ' . config('app.name') . '?')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your {$car->year} {$car->make->name} {$car->carModel->name} has been delivered — we hope you're enjoying it.")
            ->line("We'd love to hear about your experience. It only takes a minute and helps other buyers make their decision.")
            ->action('Leave a Review', route('dashboard.reviews'))
            ->line('Thank you for choosing ' . config('app.name') . '.');
    }

    public function toGiantSms(object $notifiable): GiantSmsMessage
    {
        $car = $this->order->car;
        $url = route('dashboard.reviews');

        return new GiantSmsMessage(
            "Hi {$notifiable->name}, your {$car->year} {$car->make->name} {$car->carModel->name} has been delivered! Share your experience: {$url}"
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $car = $this->order->car;

        return [
            'title' => 'How was your experience?',
            'message' => "Leave a review for your {$car->year} {$car->make->name} {$car->carModel->name}.",
            'icon' => 'star',
            'action_url' => route('dashboard.reviews'),
            'action_text' => 'Leave a Review',
        ];
    }
}
