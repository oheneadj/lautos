<?php

/**
 * Single order detail view with shipment timeline, payment proof upload,
 * and payment instruction display.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Customer;

use App\Enums\OrderStatus;
use App\Events\PaymentProofUploaded;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Order Details')]
#[Layout('layouts.app')]
class OrderDetail extends Component
{
    use WithFileUploads;

    public Order $order;

    #[Validate('required|file|mimes:jpg,jpeg,png,pdf|max:10240')]
    public $paymentProofFile = null;

    public string $transactionNote = '';

    public function mount(Order $order): void
    {
        // I verify ownership — no customer should access another customer's order.
        abort_unless($order->user_id === Auth::id(), 403);

        $this->order = $order;
        $this->refreshOrder();
    }

    /**
     * Reloads the order from the database — called on mount and by the
     * polling timer so an admin advancing the stage shows up here without
     * the customer needing to refresh the page themselves.
     */
    public function refreshOrder(): void
    {
        $this->order->refresh()->load([
            'car.make', 'car.carModel', 'car.images',
            'statusHistories', 'paymentProofs',
        ]);
    }

    /**
     * Upload a payment proof and advance status to PaymentUploaded if still PendingPayment.
     */
    public function uploadPaymentProof(): void
    {
        $this->validate([
            'paymentProofFile' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'transactionNote'  => ['nullable', 'string', 'max:500'],
        ]);

        $path = $this->paymentProofFile->store(
            "payment-proofs/{$this->order->uuid}",
            'private'
        );

        $this->order->paymentProofs()->create([
            'file_path' => $path,
            'note'      => $this->transactionNote ?: null,
        ]);

        // Advance the pipeline if we're still waiting for payment.
        if ($this->order->status === OrderStatus::PendingPayment) {
            $this->order->update(['status' => OrderStatus::PaymentUploaded]);
        }

        $this->reset(['paymentProofFile', 'transactionNote']);
        $this->order->refresh();

        PaymentProofUploaded::dispatch($this->order);

        $this->dispatch('toast', message: __('Payment proof uploaded successfully.'));
    }

    #[Computed]
    public function canUploadProof(): bool
    {
        return in_array($this->order->status, [
            OrderStatus::PendingPayment,
            OrderStatus::PaymentUploaded,
        ]);
    }

    #[Computed]
    public function pipeline(): array
    {
        $currentIndex = array_search($this->order->status, OrderStatus::cases());
        $histories = $this->order->statusHistories->keyBy('status');

        return collect(OrderStatus::pipeline())->map(function ($stage, $index) use ($currentIndex, $histories) {
            $history = $histories->get($stage->value);

            return [
                'label'     => $stage->label(),
                'value'     => $stage->value,
                'completed' => $index < $currentIndex,
                'current'   => $index === $currentIndex,
                'future'    => $index > $currentIndex,
                'date'      => $history?->created_at?->format('M d, Y'),
            ];
        })->all();
    }

    #[Computed]
    public function paymentInfo(): array
    {
        return [
            'bank_name'      => Setting::get('bank_name', '—'),
            'account_name'   => Setting::get('account_name', '—'),
            'account_number' => Setting::get('account_number', '—'),
            'momo_number'    => Setting::get('momo_number', '—'),
            'momo_name'      => Setting::get('momo_name', '—'),
        ];
    }

    #[Computed]
    public function showDemurrageWarning(): bool
    {
        return $this->order->status === OrderStatus::ArrivedInGhana;
    }

    public function render()
    {
        return view('livewire.customer.order-detail');
    }
}
