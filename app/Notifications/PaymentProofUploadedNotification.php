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

class PaymentProofUploadedNotification extends Notification implements ShouldQueue
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
        // I only send mail for now — SMS (Arkesel) is wired up in Epic 21.
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $car = $this->order->car;

        return (new MailMessage)
            ->subject('Payment Proof Uploaded — Review Needed')
            ->greeting('New payment proof to review')
            ->line("{$this->order->user->name} uploaded a payment proof for the {$car->year} {$car->make->name} {$car->carModel->name}.")
            ->action('Review in Admin', url('/admin'));
    }
}
