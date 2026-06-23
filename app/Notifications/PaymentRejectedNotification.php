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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $car = $this->order->car;

        return (new MailMessage)
            ->subject('Payment Proof Rejected — Action Needed')
            ->greeting("Hi {$notifiable->name},")
            ->line("Order reference: {$this->order->reference}")
            ->line("We couldn't verify your payment proof for the {$car->year} {$car->make->name} {$car->carModel->name}.")
            ->line("Reason: {$this->reason}")
            ->line('Please upload a new payment proof from your dashboard.')
            ->action('Upload New Proof', route('dashboard.orders.show', $this->order->uuid))
            ->line('Contact us if you have any questions.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $car = $this->order->car;

        return [
            'title' => 'Payment Proof Rejected',
            'message' => "We couldn't verify your payment proof for the {$car->year} {$car->make->name} {$car->carModel->name}. Reason: {$this->reason}",
            'icon' => 'document',
            'action_url' => route('dashboard.orders.show', $this->order->uuid),
            'action_text' => 'Upload New Proof',
        ];
    }
}
