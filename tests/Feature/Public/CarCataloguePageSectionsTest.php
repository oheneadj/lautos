<?php

namespace Tests\Feature\Public;

use App\Enums\BlogStatus;
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
 * Tests the "Browse by make" and "Latest news" sections above the catalogue page's footer.
 */
class CarCataloguePageSectionsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_makes_with_cars_in_the_browse_by_make_section(): void
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);

        $this->get(route('cars.index'))
            ->assertOk()
            ->assertSee('Browse by make')
            ->assertSee('Toyota');
    }

    #[Test]
    public function it_shows_published_posts_in_the_latest_news_section(): void
    {
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);
        $author = User::factory()->create();

        $post = BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'A Published Post',
            'slug' => 'a-published-post',
            'excerpt' => 'An excerpt.',
            'body' => 'Body content.',
            'status' => BlogStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $draft = BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'A Draft Post',
            'slug' => 'a-draft-post',
            'excerpt' => 'An excerpt.',
            'body' => 'Body content.',
            'status' => BlogStatus::Draft,
            'published_at' => null,
        ]);

        $this->get(route('cars.index'))
            ->assertOk()
            ->assertSee('Latest news')
            ->assertSee($post->title)
            ->assertDontSee($draft->title);
    }

    #[Test]
    public function it_hides_the_latest_news_section_when_there_are_no_published_posts(): void
    {
        $this->get(route('cars.index'))
            ->assertOk()
            ->assertDontSee('Latest news');
    }
}
