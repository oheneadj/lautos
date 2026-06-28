<?php

namespace Tests\Feature\Public;

use App\Enums\BlogStatus;
use App\Enums\CarStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Faq;
use App\Models\Make;
use App\Models\Setting;
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
            ->assertSee('<link rel="canonical" href="'.url('/').'">', false);
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
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('twitter:card', false);
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

        $response = $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('<title>How to Import a Car | Livingston Autos</title>', false)
            ->assertSee('<meta name="description" content="A short guide to importing your first car.">', false);

        // Article schema should reflect the real author and post, not placeholder copy.
        $response->assertSee('"@type":"Article"', false);
        $response->assertSee($author->name, false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
    }

    #[Test]
    public function the_cars_and_blog_listings_canonicalize_every_filtered_or_paginated_url_back_to_the_base_url(): void
    {
        $this->get('/cars?make=Toyota&page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.url('/cars').'">', false);

        $this->get('/blog?page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.url('/blog').'">', false);
    }

    #[Test]
    public function every_public_page_carries_the_site_wide_organization_schema_from_settings(): void
    {
        Setting::set('site_name', 'Livingston Autos');
        Setting::set('contact_email', 'info@livingstonautos.test');

        $response = $this->get('/')->assertOk();

        $response->assertSee('"@type":"Organization"', false);
        $response->assertSee('info@livingstonautos.test', false);
    }

    #[Test]
    public function the_faqs_page_has_a_faqpage_schema_matching_the_real_database_rows(): void
    {
        Faq::create(['question' => 'How long does shipping take?', 'answer' => 'Usually five to seven weeks.', 'sort_order' => 1]);

        $response = $this->get(route('pages.faqs'))->assertOk();

        $response->assertSee('"@type":"FAQPage"', false);
        $response->assertSee('How long does shipping take?', false);
        $response->assertSee('Usually five to seven weeks.', false);
    }

    #[Test]
    public function every_static_page_has_its_own_real_title_instead_of_the_generic_fallback(): void
    {
        $this->get('/how-it-works')->assertSee('<title>How It Works — Importing a Car | Livingston Autos</title>', false);
        $this->get('/shipping-and-delivery')->assertSee('<title>Shipping & Delivery | Livingston Autos</title>', false);
        $this->get('/customs-clearance')->assertSee('<title>Customs Clearance Guide | Livingston Autos</title>', false);
        $this->get('/quality-guarantee')->assertSee('<title>Vehicle Inspections & Quality Guarantee | Livingston Autos</title>', false);
        $this->get('/faqs')->assertSee('<title>Frequently Asked Questions | Livingston Autos</title>', false);
        $this->get('/refund-policy')->assertSee('<title>Refund Policy | Livingston Autos</title>', false);
        $this->get('/terms-and-conditions')->assertSee('<title>Terms & Conditions | Livingston Autos</title>', false);
        $this->get('/privacy-policy')->assertSee('<title>Privacy Policy | Livingston Autos</title>', false);
        $this->get('/fraud-awareness')->assertSee('<title>Fraud Awareness | Livingston Autos</title>', false);
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
