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

class PaymentConfirmedNotification extends Notification implements ShouldQueue
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
        // I only send mail + database for now — SMS (Arkesel) is wired up in Epic 21.
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $car = $this->order->car;

        return (new MailMessage)
            ->subject('Payment Confirmed — Your Order is Reserved')
            ->greeting("Hi {$notifiable->name},")
            ->line("Order reference: {$this->order->reference}")
            ->line("We've confirmed your payment for the {$car->year} {$car->make->name} {$car->carModel->name}.")
            ->line('Your car is now reserved and we will begin processing your purchase.')
            ->action('View Your Order', route('dashboard.orders.show', $this->order->uuid))
            ->line('Thank you for choosing Livingston Autos.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $car = $this->order->car;

        return [
            'title' => 'Payment Confirmed',
            'message' => "We've confirmed your payment for the {$car->year} {$car->make->name} {$car->carModel->name}. Your car is now reserved.",
            'icon' => 'check',
            'action_url' => route('dashboard.orders.show', $this->order->uuid),
            'action_text' => 'View Order',
        ];
    }
}
