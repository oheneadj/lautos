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

class OrderCancelledByAdminNotification extends Notification implements ShouldQueue
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
            ->subject('Order Cancelled')
            ->greeting("Hi {$notifiable->name},")
            ->line("Order reference: {$this->order->reference}")
            ->line("Your order for the {$car->year} {$car->make->name} {$car->carModel->name} has been cancelled.")
            ->line("Reason: {$this->reason}")
            ->action('Browse Other Cars', route('cars.index'))
            ->line('Contact us if you have any questions.');
    }

    public function toGiantSms(object $notifiable): GiantSmsMessage
    {
        $car = $this->order->car;

        return new GiantSmsMessage(
            "Hi {$notifiable->name}, your order {$this->order->reference} for the {$car->year} {$car->make->name} {$car->carModel->name} has been cancelled. Reason: {$this->reason}"
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $car = $this->order->car;

        return [
            'title' => 'Order Cancelled',
            'message' => "Your order for the {$car->year} {$car->make->name} {$car->carModel->name} was cancelled. Reason: {$this->reason}",
            'icon' => 'document',
            'action_url' => route('cars.index'),
            'action_text' => 'Browse Other Cars',
        ];
    }
}
