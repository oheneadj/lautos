<?php

/**
 * Customer Dashboard Overview — the landing page after login.
 *
 * Shows greeting, KYC/email verification alerts, stat cards, recent orders,
 * and an order-stage donut summary.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Customer;

use App\Enums\KycStatus;
use App\Enums\OrderStatus;
use App\Models\BlogPost;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
#[Layout('layouts.app')]
class Dashboard extends Component
{
    /**
     * Fortify's email verification link redirects here with ?verified=1 —
     * I surface that as a toast so the customer actually notices their
     * email is now confirmed, instead of landing on a dashboard that looks
     * no different than before they clicked the link.
     */
    public function mount(): void
    {
        if (request()->query('verified') === '1') {
            $this->dispatch('toast', message: __('Your email address has been verified.'));
        }
    }

    /**
     * Resend the email verification notification.
     */
    public function resendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();

        $this->dispatch('toast', message: __('Verification email sent.'));
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function needsEmailVerification(): bool
    {
        $user = Auth::user();

        return $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail();
    }

    #[Computed]
    public function needsKyc(): bool
    {
        $user = Auth::user();

        return $user->kyc_status !== KycStatus::Verified;
    }

    #[Computed]
    public function totalOrders(): int
    {
        return Auth::user()->orders()->count();
    }

    #[Computed]
    public function savedCarsCount(): int
    {
        return Auth::user()->savedCars()->count();
    }

    #[Computed]
    public function openTicketsCount(): int
    {
        return Auth::user()->supportTickets()->where('status', 'Open')->count();
    }

    /**
     * Sums price + shipping across every order the customer has ever
     * placed — reuses Order's own total accessor rather than duplicating
     * that math here.
     */
    #[Computed]
    public function totalSpendUsdCents(): int
    {
        return Auth::user()->orders()
            ->get(['price_usd_cents', 'shipping_cost_usd_cents'])
            ->sum(fn ($order) => $order->total_usd_cents);
    }

    #[Computed]
    public function recentOrders()
    {
        return Auth::user()->orders()
            ->with(['car.make', 'car.carModel', 'car.images'])
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function ordersByStage(): array
    {
        // I count per status in the database rather than pulling every order
        // row just to tally them in PHP.
        $counts = Auth::user()->orders()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stages = [];

        foreach (OrderStatus::cases() as $status) {
            $count = $counts[$status->value] ?? 0;
            if ($count > 0) {
                $stages[] = [
                    'label' => $status->label(),
                    'count' => $count,
                    'colour' => $status->colour(),
                    'value' => $status->value,
                ];
            }
        }

        return $stages;
    }

    #[Computed]
    public function recentSavedCars()
    {
        return Auth::user()->savedCars()
            ->with(['make', 'carModel', 'images'])
            ->latest('car_user.created_at')
            ->take(2)
            ->get();
    }

    #[Computed]
    public function latestBlogPosts()
    {
        return BlogPost::published()
            ->latest('published_at')
            ->take(1)
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.dashboard');
    }
}
