<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $reason)
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
        $car = $this->order->car;

        return (new MailMessage)
            ->subject('Payment Proof Rejected — Action Needed')
            ->greeting("Hi {$notifiable->name},")
            ->line("We couldn't verify your payment proof for the {$car->year} {$car->make->name} {$car->carModel->name}.")
            ->line("Reason: {$this->reason}")
            ->line('Please upload a new payment proof from your dashboard.')
            ->action('Upload New Proof', route('dashboard'))
            ->line('Contact us if you have any questions.');
    }
}
