<?php

namespace Tests\Feature\Customer;

use App\Enums\OrderStatus;
use App\Enums\ReviewStatus;
use App\Livewire\Customer\Reviews;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the customer-facing review management page — writing a review for
 * a delivered order, and seeing the moderation status of ones already sent.
 */
class ReviewsTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(): Car
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
        ]);
    }

    #[Test]
    public function a_guest_cannot_access_the_reviews_page(): void
    {
        $this->get(route('dashboard.reviews'))->assertRedirect(route('login'));
    }

    #[Test]
    public function it_lists_delivered_orders_awaiting_a_review(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Delivered, 'car_id' => $this->makeCar()->id]);

        $this->actingAs($user)->get(route('dashboard.reviews'))
            ->assertOk()
            ->assertSee('Write a Review');
    }

    #[Test]
    public function a_non_delivered_order_does_not_show_a_review_prompt(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Shipped, 'car_id' => $this->makeCar()->id]);

        $this->actingAs($user)->get(route('dashboard.reviews'))
            ->assertOk()
            ->assertDontSee('Write a Review');
    }

    #[Test]
    public function submitting_a_review_creates_it_as_pending(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Delivered, 'car_id' => $this->makeCar()->id]);

        Livewire::actingAs($user)->test(Reviews::class)
            ->call('startReview', $order->uuid)
            ->set('rating', 5)
            ->set('title', 'Great service')
            ->set('body', 'Everything went smoothly from start to finish.')
            ->call('submitReview');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'rating' => 5,
            'title' => 'Great service',
            'status' => ReviewStatus::Pending->value,
        ]);
    }

    #[Test]
    public function submitting_a_review_requires_a_title_and_body(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Delivered, 'car_id' => $this->makeCar()->id]);

        Livewire::actingAs($user)->test(Reviews::class)
            ->call('startReview', $order->uuid)
            ->call('submitReview')
            ->assertHasErrors(['title', 'body']);

        $this->assertDatabaseCount('reviews', 0);
    }

    #[Test]
    public function a_customer_cannot_submit_a_second_review_for_the_same_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Delivered, 'car_id' => $this->makeCar()->id]);
        Review::factory()->for($user)->for($order)->create();

        Livewire::actingAs($user)->test(Reviews::class)
            ->call('startReview', $order->uuid)
            ->set('title', 'Second attempt')
            ->set('body', 'Trying to review again.')
            ->call('submitReview');

        $this->assertDatabaseCount('reviews', 1);
    }

    #[Test]
    public function a_customer_cannot_review_another_customers_order(): void
    {
        $user = User::factory()->create();
        $otherOrder = Order::factory()->create(['status' => OrderStatus::Delivered, 'car_id' => $this->makeCar()->id]);

        Livewire::actingAs($user)->test(Reviews::class)
            ->call('startReview', $otherOrder->uuid)
            ->set('title', 'Not mine')
            ->set('body', 'This order is not mine.')
            ->call('submitReview');

        $this->assertDatabaseCount('reviews', 0);
    }

    #[Test]
    public function it_shows_the_moderation_status_of_submitted_reviews(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Delivered, 'car_id' => $this->makeCar()->id]);
        Review::factory()->for($user)->for($order)->approved()->create(['title' => 'Loved it']);

        $this->actingAs($user)->get(route('dashboard.reviews'))
            ->assertOk()
            ->assertSee('Loved it')
            ->assertSee('Approved');
    }
}
