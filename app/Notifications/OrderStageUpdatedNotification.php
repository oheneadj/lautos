<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use App\Enums\OrderStatus;
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
            ->line("Order reference: {$this->order->reference}")
            ->line("Your order for the {$car->year} {$car->make->name} {$car->carModel->name} has moved to: {$stage}.");

        if ($this->order->estimated_arrival_date) {
            $mail->line('Estimated arrival in Ghana: ' . $this->order->estimated_arrival_date->format('M j, Y'));
        }

        // The clearing/demurrage warning is the one piece of stage-specific
        // content the SRS notification matrix calls out by name — every
        // other stage just gets the generic "moved to X" line above.
        if ($this->order->status === OrderStatus::ArrivedInGhana) {
            $mail->line('Your car has arrived at Tema Port. Please arrange customs clearance promptly to avoid demurrage and storage penalties.');
        }

        if ($this->order->status === OrderStatus::Delivered) {
            $mail->line('Your car has been delivered. Thank you for choosing Livingston Autos!');
        }

        return $mail
            ->action('Track Your Order', route('dashboard.index'))
            ->line('Thank you for choosing Livingston Autos.');
    }
}
