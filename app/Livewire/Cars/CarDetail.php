<?php

/**
 * @author Ohene Adjei
 */

namespace App\Livewire\Cars;

use App\Enums\CarStatus;
use App\Enums\KycStatus;
use App\Models\Car;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CarDetail extends Component
{
    public Car $car;
    public int $activeImageIndex = 0;
    public bool $showOrderModal = false;

    public function setActiveImage(int $index): void
    {
        $this->activeImageIndex = $index;
    }

    public function openOrderModal(): void
    {
        $this->showOrderModal = true;
    }

    public function closeOrderModal(): void
    {
        $this->showOrderModal = false;
    }

    #[Computed]
    public function kycIncomplete(): bool
    {
        return Auth::check() && Auth::user()->kyc_status !== KycStatus::Verified;
    }

    /**
     * Places the order and sends the customer straight to its detail page.
     * I don't block on KYC here — US-39 only requires a warning, not a hard
     * stop, since KYC just needs to be done before delivery, not ordering.
     */
    public function confirmOrder(): void
    {
        abort_unless(Auth::check(), 403);

        if ($this->car->status !== CarStatus::Available) {
            $this->addError('order', 'Sorry, this car was just reserved by someone else.');

            return;
        }

        $order = app(OrderService::class)->createOrder(Auth::user(), $this->car);

        $this->redirectRoute('dashboard.orders.show', $order->uuid, navigate: true);
    }

    public function render()
    {
        return view('livewire.cars.car-detail');
    }
}
