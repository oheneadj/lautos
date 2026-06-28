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

    public function __construct(public Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // This goes to the admin's business email, not a customer with a
        // phone number, so there's no SMS channel to add here.
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
            ->subject('Payment Proof Uploaded — Review Needed')
            ->greeting('New payment proof to review')
            ->line("{$this->order->user->name} uploaded a payment proof for the {$this->order->car_year} {$this->order->car_make_name} {$this->order->car_model_name}.")
            ->action('Review in Admin', url('/admin'));
    }
}
