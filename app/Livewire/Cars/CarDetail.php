<?php

/**
 * @author Ohene Adjei
 */

namespace App\Livewire\Cars;

use App\Concerns\BuildsLoginRedirectUrl;
use App\Enums\CarStatus;
use App\Enums\KycStatus;
use App\Enums\OrderStatus;
use App\Models\Car;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class CarDetail extends Component
{
    use BuildsLoginRedirectUrl;

    public Car $car;
    public int $activeImageIndex = 0;
    public bool $showOrderModal = false;

    // Flips to true once resendVerification() succeeds, so the modal can
    // show a confirmation in place of the button instead of relying on a
    // toast event that nothing on the public layout listens for.
    public bool $verificationJustSent = false;

    /**
     * Set when a guest is sent to login from the "Login to Order" button —
     * lets us reopen the order modal automatically once they're back here
     * signed in, instead of making them click Order again from scratch.
     */
    #[Url]
    public ?string $intent = null;

    /**
     * The page URL the "Login to Order" link should send a guest back to. I
     * capture this once at mount time rather than calling request() from
     * the Blade view — after the first wire:click (e.g. swapping the photo
     * gallery image), every later render happens inside Livewire's own AJAX
     * update request, where request() would resolve to that internal
     * endpoint instead of the actual page.
     */
    public string $loginRedirectUrl = '';

    public function mount(Car $car): void
    {
        $this->car = $car;
        $this->loginRedirectUrl = $this->buildLoginRedirectUrl(['intent' => 'order']);

        if ($this->intent === 'order' && Auth::check()) {
            $this->showOrderModal = true;
        }
    }

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

    #[Computed]
    public function emailUnverified(): bool
    {
        return Auth::check() && ! Auth::user()->hasVerifiedEmail();
    }

    /**
     * Counts how many customers currently have an open order on this car —
     * shown as social proof. I exclude Cancelled orders since those lost
     * the race when someone else's payment got confirmed first; counting
     * them would inflate the number with people no longer in the running.
     */
    #[Computed]
    public function reservationsCount(): int
    {
        return $this->car->orders()->where('status', '!=', OrderStatus::Cancelled)->count();
    }

    /**
     * Places the order and sends the customer straight to its detail page.
     * I don't block on KYC here — US-39 only requires a warning, not a hard
     * stop, since KYC just needs to be done before delivery, not ordering.
     * Email verification is a hard stop, though — an unverified address
     * means we can't reliably reach the customer about their own order.
     */
    public function confirmOrder(): void
    {
        abort_unless(Auth::check(), 403);

        if (! Auth::user()->hasVerifiedEmail()) {
            $this->addError('order', 'Please verify your email address before placing an order.');

            return;
        }

        if ($this->car->status !== CarStatus::Available) {
            $this->addError('order', 'Sorry, this car was just reserved by someone else.');

            return;
        }

        $order = app(OrderService::class)->createOrder(Auth::user(), $this->car);

        $this->redirectRoute('dashboard.orders.show', $order->uuid, navigate: true);
    }

    /**
     * Resends the email verification notification from the order modal,
     * so an unverified customer doesn't have to leave the page to fix it.
     */
    public function resendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();

        $this->verificationJustSent = true;
        $this->dispatch('toast', message: __('Verification email sent.'));
    }

    public function render()
    {
        return view('livewire.cars.car-detail');
    }
}
