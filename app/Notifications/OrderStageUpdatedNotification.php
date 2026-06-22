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

class OrderStageUpdatedNotification extends Notification implements ShouldQueue
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
        $stage = $this->order->status->label();

        $mail = (new MailMessage)
            ->subject("Order Update — {$stage}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your order for the {$car->year} {$car->make->name} {$car->carModel->name} has moved to: {$stage}.");

        if ($this->order->estimated_arrival_date) {
            $mail->line('Estimated arrival in Ghana: ' . $this->order->estimated_arrival_date->format('M j, Y'));
        }

        return $mail
            ->action('Track Your Order', route('dashboard.index'))
            ->line('Thank you for choosing Livingston Autos.');
    }
}
