<?php

namespace Tests\Unit\Jobs;

use App\Enums\BlogStatus;
use App\Enums\CarStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests sitemap generation (US-34) — included cars/blog posts and the
 * static pages, written to public/sitemap.xml.
 */
class GenerateSitemapTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        File::delete(public_path('sitemap.xml'));
        parent::tearDown();
    }

    private function makePost(array $attributes = []): BlogPost
    {
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);
        $author = User::factory()->create();

        return BlogPost::create(array_merge([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'A Post',
            'slug' => 'a-post-' . uniqid(),
            'excerpt' => 'An excerpt.',
            'body' => 'Body content.',
            'status' => BlogStatus::Published,
            'published_at' => now()->subDay(),
        ], $attributes));
    }

    #[Test]
    public function it_includes_available_cars_and_published_posts_and_static_pages(): void
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => CarStatus::Available,
        ]);
        $post = $this->makePost();

        $this->artisan('sitemap:generate')->assertSuccessful();

        $xml = File::get(public_path('sitemap.xml'));

        $this->assertStringContainsString(route('cars.show', $car->slug), $xml);
        $this->assertStringContainsString(route('blog.show', $post->slug), $xml);
        $this->assertStringContainsString(route('home'), $xml);
        $this->assertStringContainsString(route('about'), $xml);
    }

    #[Test]
    public function it_excludes_sold_cars_and_unpublished_posts(): void
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);
        $soldCar = Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => CarStatus::Sold,
            'sold_at' => now()->subDays(30),
        ]);
        $draftPost = $this->makePost(['status' => BlogStatus::Draft, 'published_at' => null]);

        $this->artisan('sitemap:generate')->assertSuccessful();

        $xml = File::get(public_path('sitemap.xml'));

        $this->assertStringNotContainsString(route('cars.show', $soldCar->slug), $xml);
        $this->assertStringNotContainsString(route('blog.show', $draftPost->slug), $xml);
    }
}
