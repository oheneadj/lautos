<?php

namespace Tests\Feature\Public;

use App\Enums\BlogStatus;
use App\Enums\CarStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests that every public page gets its own unique title, meta description,
 * and canonical URL — not just a shared default (US-34).
 */
class SeoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_homepage_has_a_unique_title_and_canonical_url(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Livingston Autos — Quality Japanese & Korean Imports</title>', false)
            ->assertSee('<link rel="canonical" href="' . url('/') . '">', false);
    }

    #[Test]
    public function the_cars_index_has_its_own_title_distinct_from_the_homepage(): void
    {
        $this->get('/cars')
            ->assertOk()
            ->assertSee('<title>Cars for Sale | Livingston Autos</title>', false);
    }

    #[Test]
    public function a_car_detail_page_has_a_unique_title_and_product_json_ld(): void
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => CarStatus::Available,
        ]);

        $response = $this->get(route('cars.show', $car->slug))->assertOk();

        $response->assertSee("{$car->year} {$car->make->name} {$car->carModel->name}", false);
        $response->assertSee('"@type":"Product"', false);
        $response->assertSee('"@type":"Offer"', false);
    }

    #[Test]
    public function the_about_and_contact_pages_have_distinct_titles(): void
    {
        $this->get('/about')->assertSee('<title>About Us | Livingston Autos</title>', false);
        $this->get('/contact')->assertSee('<title>Contact Us | Livingston Autos</title>', false);
    }

    #[Test]
    public function a_blog_post_page_has_its_own_title_and_description(): void
    {
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);
        $author = User::factory()->create();
        $post = BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'How to Import a Car',
            'slug' => 'how-to-import-a-car',
            'excerpt' => 'A short guide to importing your first car.',
            'body' => 'Body content.',
            'status' => BlogStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('<title>How to Import a Car | Livingston Autos</title>', false)
            ->assertSee('<meta name="description" content="A short guide to importing your first car.">', false);
    }

    #[Test]
    public function robots_txt_blocks_the_admin_panel(): void
    {
        // robots.txt is a static file served by the webserver, not routed through
        // the framework — I read it directly rather than making an HTTP request.
        $contents = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /admin', $contents);
    }
}
