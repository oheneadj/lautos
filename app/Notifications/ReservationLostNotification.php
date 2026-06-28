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

class ReservationLostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

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

        $mail = (new MailMessage)
            ->subject('Car No Longer Available — Another Buyer Completed Payment')
            ->greeting("Hi {$notifiable->name},")
            ->line("Order reference: {$this->order->reference}")
            ->line("We're sorry — another buyer completed payment for the {$this->order->car_year} {$this->order->car_make_name} {$this->order->car_model_name} before you, so this order has been cancelled.");

        if ($this->order->paymentProofs->isNotEmpty()) {
            $mail->line("Since you'd already submitted payment proof, please contact us so we can arrange a refund.");
        }

        return $mail
            ->action('Browse Other Cars', route('cars.index'))
            ->line('Thank you for your interest in '.config('app.name').'.');
    }

    public function toGiantSms(object $notifiable): GiantSmsMessage
    {

        return new GiantSmsMessage(
            "Hi {$notifiable->name}, the {$this->order->car_year} {$this->order->car_make_name} {$this->order->car_model_name} from order {$this->order->reference} is no longer available — another buyer completed payment first."
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {

        return [
            'title' => 'Car No Longer Available',
            'message' => "Another buyer completed payment for the {$this->order->car_year} {$this->order->car_make_name} {$this->order->car_model_name} first, so this order was cancelled.",
            'icon' => 'document',
            'action_url' => route('cars.index'),
            'action_text' => 'Browse Other Cars',
        ];
    }
}
