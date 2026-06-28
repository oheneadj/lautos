<?php

/**
 * Homepage "What our customers say" carousel. I pulled this out of
 * welcome.blade.php into its own lazy-loaded component so its review query
 * doesn't run on every homepage hit before the above-the-fold content paints.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Home;

use App\Models\Review;
use Livewire\Component;

class Testimonials extends Component
{
    /**
     * I show a skeleton row while this lazy-loads, matching the real
     * carousel's card dimensions so the page doesn't jump once it's ready.
     */
    public function placeholder(): string
    {
        return <<<'HTML'
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="h-[220px] rounded-2xl bg-gray-100 animate-pulse"></div>
                <div class="h-[220px] rounded-2xl bg-gray-100 animate-pulse"></div>
                <div class="h-[220px] rounded-2xl bg-gray-100 animate-pulse"></div>
            </div>
            HTML;
    }

    public function render()
    {
        // I only ever show approved reviews here — pending/rejected ones
        // never reach the public site.
        $approvedReviews = Review::approved()
            ->with(['user', 'order.car.make', 'order.car.carModel'])
            ->latest('approved_at')
            ->limit(9)
            ->get();

        $demoReviews = [
            ['author' => 'Richg321', 'subtitle' => 'from The Villages, FL', 'date' => '03/29/2026', 'rating' => 5, 'title' => 'Amazing car, comfortable, smooth ride', 'body' => 'Amazing car, comfortable, smooth ride very quiet solid performance. Averaging 35 miles per gallon. That kind of blew me away. Great turn ratio smooth turning radius.'],
            ['author' => 'JamesVZ', 'subtitle' => 'from South Bend, IN', 'date' => '03/05/2026', 'rating' => 5, 'title' => 'Great experience with the dealer', 'body' => 'Great experience with the dealer. Have about 300 miles on the new car so far and I really like it. You can\'t beat the brand for innovation and warranty.'],
            ['author' => 'William H.', 'subtitle' => 'from Round Lake Beach', 'date' => '11/22/2025', 'rating' => 4, 'title' => 'Very comfortable and nice to drive', 'body' => 'I purchased the Kia Sportage EX Hybrid model. It is very comfortable and nice to drive. There are many extras with this model. The warranty is outstanding.'],
        ];

        // A brand-new (or sparsely-reviewed) site shouldn't show an empty or
        // half-empty testimonials carousel, so I fall back to the demo set
        // until there are at least 3 real approved reviews to show instead.
        if ($approvedReviews->count() < 3) {
            $reviews = $demoReviews;
        } else {
            $reviews = $approvedReviews->map(function ($review) {
                $order = $review->order;

                return [
                    'author' => $review->user->name,
                    'subtitle' => "Verified Buyer — {$order->car_year} {$order->car_make_name} {$order->car_model_name}",
                    'date' => $review->approved_at?->format('m/d/Y') ?? $review->created_at->format('m/d/Y'),
                    'rating' => $review->rating,
                    'title' => $review->title,
                    'body' => $review->body,
                ];
            })->all();
        }

        // I group reviews into slides of 3 so the carousel shows three cards
        // side by side per slide, instead of one centered card with empty
        // space either side.
        $reviewGroups = array_chunk($reviews, 3);

        return view('livewire.home.testimonials', compact('reviewGroups'));
    }
}
