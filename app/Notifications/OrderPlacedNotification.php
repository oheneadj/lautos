<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use App\Channels\GiantSmsMessage;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];

        // I only add the SMS channel when there's actually a phone number to
        // send to — otherwise Laravel still queues a no-op GiantSMS job.
        if (! empty($notifiable->phone)) {
            $channels[] = 'giantsms';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $total = number_format($this->order->total_usd_cents / 100, 0);

        return (new MailMessage)
            ->subject('Order Confirmed — Payment Instructions')
            ->greeting("Hi {$notifiable->name},")
            ->line("Order reference: {$this->order->reference}")
            ->line("We've reserved the {$this->order->car_year} {$this->order->car_make_name} {$this->order->car_model_name} for you. Total due: \${$total}.")
            ->line('Bank: '.Setting::get('bank_name', '—').' · Account: '.Setting::get('account_number', '—').' ('.Setting::get('account_name', '—').')')
            ->line('Mobile Money: '.Setting::get('momo_number', '—').' ('.Setting::get('momo_name', '—').')')
            ->action('View Your Order', route('dashboard.orders.show', $this->order->uuid))
            ->line('Once we receive your payment proof, we will confirm your order and begin processing your purchase.');
    }

    public function toGiantSms(object $notifiable): GiantSmsMessage
    {
        $total = number_format($this->order->total_usd_cents / 100, 0);
        $url = route('dashboard.orders.show', $this->order->uuid);

        return new GiantSmsMessage(
            "Hi {$notifiable->name}, your order {$this->order->reference} for the {$this->order->car_year} {$this->order->car_make_name} {$this->order->car_model_name} is confirmed. Total: \${$total}. Upload payment proof at {$url}"
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $total = number_format($this->order->total_usd_cents / 100, 0);

        return [
            'title' => 'Order Confirmed',
            'message' => "We've reserved the {$this->order->car_year} {$this->order->car_make_name} {$this->order->car_model_name} for you. Total due: \${$total}.",
            'icon' => 'check',
            'action_url' => route('dashboard.orders.show', $this->order->uuid),
            'action_text' => 'View Order',
        ];
    }
}
