<?php

/**
 * @author Ohene Adjei
 */

namespace App\Notifications;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
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
        $total = number_format($this->order->total_usd_cents / 100, 0);

        return (new MailMessage)
            ->subject('Order Confirmed — Payment Instructions')
            ->greeting("Hi {$notifiable->name},")
            ->line("Order reference: {$this->order->reference}")
            ->line("We've reserved the {$car->year} {$car->make->name} {$car->carModel->name} for you. Total due: \${$total}.")
            ->line('Bank: '.Setting::get('bank_name', '—').' · Account: '.Setting::get('account_number', '—').' ('.Setting::get('account_name', '—').')')
            ->line('Mobile Money: '.Setting::get('momo_number', '—').' ('.Setting::get('momo_name', '—').')')
            ->action('View Your Order', route('dashboard.orders.show', $this->order->uuid))
            ->line('Once we receive your payment proof, we will confirm your order and begin processing your purchase.');
    }
}
