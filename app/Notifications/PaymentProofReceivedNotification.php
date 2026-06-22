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

class PaymentProofReceivedNotification extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $car = $this->order->car;

        return (new MailMessage)
            ->subject('Payment Proof Received')
            ->greeting("Hi {$notifiable->name},")
            ->line("Order reference: {$this->order->reference}")
            ->line("We've received your payment proof for the {$car->year} {$car->make->name} {$car->carModel->name}.")
            ->line("We're reviewing it now and will confirm your order shortly.")
            ->action('View Your Order', route('dashboard.orders.show', $this->order->uuid));
    }
}
