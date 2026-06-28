<?php

namespace Tests\Feature\Customer;

use App\Enums\BlogStatus;
use App\Enums\KycStatus;
use App\Enums\OrderStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the customer dashboard overview page (US-38).
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(): Car
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
    }

    #[Test]
    public function guest_cannot_view_the_dashboard(): void
    {
        $this->get(route('dashboard.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function landing_on_the_dashboard_after_verifying_email_shows_a_confirmation_toast(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.index', ['verified' => '1']))
            ->assertSee('Your email address has been verified.');
    }

    #[Test]
    public function visiting_the_dashboard_without_the_verified_flag_shows_no_confirmation_toast(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertDontSee('Your email address has been verified.');
    }

    #[Test]
    public function it_greets_the_customer_by_name_and_shows_a_browse_cars_cta(): void
    {
        $user = User::factory()->create(['name' => 'Ama Boateng']);

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Ama Boateng')
            ->assertSee('Browse More Cars');
    }

    #[Test]
    public function it_shows_a_kyc_incomplete_banner_until_kyc_is_verified(): void
    {
        $user = User::factory()->create(['kyc_status' => KycStatus::Pending]);

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertSee('KYC Incomplete');

        $user->update(['kyc_status' => KycStatus::Verified]);

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertDontSee('KYC Incomplete');
    }

    #[Test]
    public function the_sidebar_shows_a_verified_badge_once_kyc_is_verified(): void
    {
        $user = User::factory()->create(['kyc_status' => KycStatus::Verified]);

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertSee('Verified')
            ->assertDontSee('Unverified');
    }

    #[Test]
    public function the_sidebar_shows_an_unverified_badge_for_pending_and_resubmission_kyc(): void
    {
        $pending = User::factory()->create(['kyc_status' => KycStatus::Pending]);

        $this->actingAs($pending)
            ->get(route('dashboard.index'))
            ->assertSee('Unverified');

        $needsResubmission = User::factory()->create(['kyc_status' => KycStatus::NeedsResubmission]);

        $this->actingAs($needsResubmission)
            ->get(route('dashboard.index'))
            ->assertSee('Unverified');
    }

    #[Test]
    public function it_only_lists_the_authenticated_customers_own_orders(): void
    {
        $myCar = $this->makeCar();

        $otherMake = Make::create(['name' => 'Hyundai']);
        $otherModel = CarModel::create(['make_id' => $otherMake->id, 'name' => 'Tucson']);
        $otherCar = Car::factory()->create(['make_id' => $otherMake->id, 'car_model_id' => $otherModel->id]);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Order::factory()->forCar($myCar)->create(['user_id' => $user->id, 'status' => OrderStatus::PendingPayment]);
        Order::factory()->forCar($otherCar)->create(['user_id' => $otherUser->id, 'status' => OrderStatus::Delivered]);

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertSee('Toyota');
        $response->assertDontSee('Hyundai');
    }

    #[Test]
    public function it_shows_the_lifetime_total_spend_across_all_orders(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create();

        Order::factory()->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'price_usd_cents' => 1500000,
            'shipping_cost_usd_cents' => 200000,
        ]);
        Order::factory()->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'price_usd_cents' => 800000,
            'shipping_cost_usd_cents' => 100000,
        ]);

        // Total: (15000 + 2000) + (8000 + 1000) = 26000.
        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertSee('$26,000')
            ->assertSee('TOTAL SPEND');
    }

    #[Test]
    public function the_header_button_links_to_the_notifications_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertSee(route('dashboard.notifications'), false);
    }

    #[Test]
    public function it_only_shows_the_authenticated_customers_own_saved_cars(): void
    {
        $myCar = $this->makeCar();

        $otherMake = Make::create(['name' => 'Hyundai']);
        $otherModel = CarModel::create(['make_id' => $otherMake->id, 'name' => 'Tucson']);
        $otherCar = Car::factory()->create(['make_id' => $otherMake->id, 'car_model_id' => $otherModel->id]);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $user->savedCars()->attach($myCar->id);
        $otherUser->savedCars()->attach($otherCar->id);

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertSee('Toyota');
        $response->assertDontSee('Hyundai');
    }

    #[Test]
    public function it_shows_the_two_most_recent_published_blog_posts(): void
    {
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);
        $author = User::factory()->create();

        BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'How to Clear Customs',
            'excerpt' => 'A guide.',
            'body' => 'Body.',
            'status' => BlogStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'Still a Draft',
            'excerpt' => 'A guide.',
            'body' => 'Body.',
            'status' => BlogStatus::Draft,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertSee('How to Clear Customs');
        $response->assertDontSee('Still a Draft');
    }

    #[Test]
    public function the_duplicate_recent_orders_table_is_gone(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertDontSee('All Recent Orders');
    }

    #[Test]
    public function the_support_card_links_to_open_a_ticket_and_uses_real_settings(): void
    {
        Setting::set('contact_email', 'help@livingstonautos.com');
        Setting::set('whatsapp_number', '+233 55 999 8888');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertOk()
            ->assertSee(route('dashboard.support'), escape: false)
            ->assertSee('Open a Support Ticket')
            ->assertSee('mailto:help@livingstonautos.com', escape: false)
            ->assertSee('wa.me/233559998888', escape: false)
            ->assertDontSee('Alexander Davis');
    }

    #[Test]
    public function the_support_card_shows_the_open_ticket_count_when_the_customer_has_one(): void
    {
        $user = User::factory()->create();
        $user->supportTickets()->create([
            'subject' => 'Where is my car?',
            'status' => 'Open',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertOk()->assertSee('View Support Tickets (1 open)');
    }
}
