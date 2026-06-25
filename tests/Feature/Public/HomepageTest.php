<?php

namespace Tests\Feature\Public;

use App\Enums\CarStatus;
use App\Enums\OrderStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the homepage (Epic 9) — featured cars and the floating WhatsApp
 * button that's included via the public layout on every page.
 */
class HomepageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_available_cars_as_featured(): void
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        $car = Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => CarStatus::Sold,
        ]);
        $car->update(['status' => CarStatus::Available]);

        // The "Latest arrivals" section is now a lazy-loaded Livewire component
        // (cars.latest-cars), so its content arrives via a follow-up request
        // rather than the homepage's initial HTML — I test it directly.
        $this->get('/')->assertOk();
        Livewire::test(\App\Livewire\Cars\LatestCars::class)->assertSee($carModel->name);
    }

    #[Test]
    public function the_whatsapp_button_links_to_the_configured_number_with_the_default_message(): void
    {
        Setting::set('whatsapp_number', '+233 55 123 4567');

        $this->get('/')
            ->assertOk()
            ->assertSee('wa.me/233551234567', false)
            ->assertSee('Chat with us on WhatsApp', false);
    }

    #[Test]
    public function the_whatsapp_button_is_hidden_when_no_number_is_configured(): void
    {
        Setting::set('whatsapp_number', '');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('wa.me/');
    }

    private function makeApprovedReview(string $title): Review
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
        $order = Order::factory()->create(['car_id' => $car->id, 'status' => OrderStatus::Delivered]);

        return Review::factory()->for($order)->for($order->user)->approved()->create(['title' => $title]);
    }

    #[Test]
    public function it_shows_demo_reviews_when_fewer_than_three_are_approved(): void
    {
        $this->makeApprovedReview('Only one real review so far');

        // Testimonials are now a lazy-loaded Livewire component, so I assert
        // against the component directly rather than the homepage's initial HTML.
        Livewire::test(\App\Livewire\Home\Testimonials::class)
            ->assertDontSee('Only one real review so far')
            // The hardcoded demo testimonials fill in until there are 3+ real ones.
            ->assertSee('Amazing car, comfortable, smooth ride');
    }

    #[Test]
    public function it_shows_real_approved_reviews_once_there_are_at_least_three(): void
    {
        $this->makeApprovedReview('First real review');
        $this->makeApprovedReview('Second real review');
        $this->makeApprovedReview('Third real review');

        Livewire::test(\App\Livewire\Home\Testimonials::class)
            ->assertSee('First real review')
            ->assertDontSee('Amazing car, comfortable, smooth ride');
    }

    #[Test]
    public function it_never_shows_unapproved_reviews(): void
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
        $order = Order::factory()->create(['car_id' => $car->id, 'status' => OrderStatus::Delivered]);
        Review::factory()->for($order)->for($order->user)->create(['title' => 'Still pending review']);

        Livewire::test(\App\Livewire\Home\Testimonials::class)->assertDontSee('Still pending review');
    }

    #[Test]
    public function trending_categories_are_driven_by_the_real_body_type_field(): void
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Highlander']);

        $suv = Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => CarStatus::Available,
            'body_type' => \App\Enums\CarBodyType::Suv,
        ]);

        // A model name that was never in the old hardcoded category lists —
        // this is exactly the case that used to silently vanish from every tab.
        $unknownModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Outlander Sport']);
        $alsoSuv = Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $unknownModel->id,
            'status' => CarStatus::Available,
            'body_type' => \App\Enums\CarBodyType::Suv,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee($suv->slug)
            ->assertSee($alsoSuv->slug);
    }

    #[Test]
    public function it_shows_the_latest_three_published_blog_posts(): void
    {
        $category = \App\Models\BlogCategory::create(['name' => 'Buying Guide', 'slug' => 'buying-guide']);
        $author = User::factory()->create();

        $post = \App\Models\BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'Top 5 things to check when buying a used car',
            'excerpt' => 'An excerpt.',
            'body' => 'Body content.',
            'status' => \App\Enums\BlogStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $draft = \App\Models\BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'Unpublished draft post',
            'excerpt' => 'An excerpt.',
            'body' => 'Body content.',
            'status' => \App\Enums\BlogStatus::Draft,
            'published_at' => null,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($category->name)
            ->assertDontSee($draft->title);
    }
}
