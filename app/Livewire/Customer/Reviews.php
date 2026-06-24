<?php

/**
 * Lets a customer leave a review for each delivered order, and see the
 * moderation status of reviews they've already submitted.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Customer;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Reviews')]
#[Layout('layouts.app')]
class Reviews extends Component
{
    public bool $showFormModal = false;
    public ?string $orderUuid = null;
    public int $rating = 5;
    public string $title = '';
    public string $body = '';

    /** Opens the write-a-review form for a specific delivered order. */
    public function startReview(string $orderUuid): void
    {
        $this->orderUuid = $orderUuid;
        $this->rating = 5;
        $this->title = '';
        $this->body = '';
        $this->showFormModal = true;
    }

    public function submitReview(): void
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
        ]);

        // I look this order up scoped to the current user rather than trusting
        // the uuid alone — orderUuid is just a public property, so without this
        // scoping a customer could submit a review against someone else's order.
        $order = Auth::user()->orders()
            ->where('uuid', $this->orderUuid)
            ->where('status', OrderStatus::Delivered)
            ->whereDoesntHave('review')
            ->first();

        if (! $order) {
            $this->showFormModal = false;

            return;
        }

        Review::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'rating' => $this->rating,
            'title' => $this->title,
            'body' => $this->body,
        ]);

        $this->showFormModal = false;
        $this->dispatch('toast', message: __('Thanks! Your review has been submitted for approval.'));
    }

    /**
     * Delivered orders the customer hasn't reviewed yet — these are the
     * ones that show a "Write a Review" prompt.
     */
    #[Computed]
    public function reviewableOrders()
    {
        return Auth::user()->orders()
            ->where('status', OrderStatus::Delivered)
            ->whereDoesntHave('review')
            ->with(['car.make', 'car.carModel'])
            ->latest()
            ->get();
    }

    /** Reviews the customer has already submitted, newest first. */
    #[Computed]
    public function submittedReviews()
    {
        return Auth::user()->reviews()
            ->with(['order.car.make', 'order.car.carModel'])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.reviews');
    }
}
