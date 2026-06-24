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
            ->subject('Payment Proof Received')
            ->greeting("Hi {$notifiable->name},")
            ->line("Order reference: {$this->order->reference}")
            ->line("We've received your payment proof for the {$car->year} {$car->make->name} {$car->carModel->name}.")
            ->line("We're reviewing it now and will confirm your order shortly.")
            ->action('View Your Order', route('dashboard.orders.show', $this->order->uuid));
    }

    public function toGiantSms(object $notifiable): GiantSmsMessage
    {
        return new GiantSmsMessage(
            "Hi {$notifiable->name}, we've received your payment proof for order {$this->order->reference}. We're reviewing it now."
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $car = $this->order->car;

        return [
            'title' => 'Payment Proof Received',
            'message' => "We've received your payment proof for the {$car->year} {$car->make->name} {$car->carModel->name}. We're reviewing it now.",
            'icon' => 'document',
            'action_url' => route('dashboard.orders.show', $this->order->uuid),
            'action_text' => 'View Order',
        ];
    }
}
