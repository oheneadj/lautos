<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Enums\ReviewStatus;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\ShieldPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin review moderation table — approving, rejecting, and
 * deleting customer-submitted reviews.
 */
class ReviewManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeReview(array $attributes = []): Review
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
        $order = Order::factory()->create(['car_id' => $car->id, 'status' => OrderStatus::Delivered]);

        return Review::factory()->for($order)->for($order->user)->create($attributes);
    }

    private function actingAsAdmin(): User
    {
        $this->seed(ShieldPermissionsSeeder::class);

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function a_guest_cannot_access_the_admin_review_list(): void
    {
        $this->get('/admin/reviews')->assertRedirect('/admin/login');
    }

    #[Test]
    public function it_lists_reviews_pending_moderation(): void
    {
        $this->actingAsAdmin();
        $review = $this->makeReview(['title' => 'Smooth purchase']);

        Livewire::test(ListReviews::class)
            ->assertCanSeeTableRecords([$review]);
    }

    #[Test]
    public function approving_a_review_sets_its_status_and_timestamp(): void
    {
        $this->actingAsAdmin();
        $review = $this->makeReview();

        Livewire::test(ListReviews::class)
            ->callTableAction('approve', $review);

        $review->refresh();
        $this->assertSame(ReviewStatus::Approved, $review->status);
        $this->assertNotNull($review->approved_at);
    }

    #[Test]
    public function rejecting_a_review_sets_its_status(): void
    {
        $this->actingAsAdmin();
        $review = $this->makeReview();

        Livewire::test(ListReviews::class)
            ->callTableAction('reject', $review);

        $this->assertSame(ReviewStatus::Rejected, $review->refresh()->status);
    }

    #[Test]
    public function deleting_a_review_removes_it(): void
    {
        $this->actingAsAdmin();
        $review = $this->makeReview();

        Livewire::test(ListReviews::class)
            ->callTableAction('delete', $review);

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    #[Test]
    public function a_role_without_update_review_permission_cannot_approve_or_reject(): void
    {
        $this->seed(ShieldPermissionsSeeder::class);

        $viewer = User::factory()->create(['is_admin' => true]);
        $viewer->assignRole(Role::findOrCreate('review_viewer', 'web'));
        $viewer->syncPermissions(['ViewAny:Review', 'View:Review']);
        $this->actingAs($viewer);

        $review = $this->makeReview();

        Livewire::test(ListReviews::class)
            ->assertTableActionHidden('approve', $review)
            ->assertTableActionHidden('reject', $review);

        $this->assertSame(ReviewStatus::Pending, $review->refresh()->status);
    }
}
