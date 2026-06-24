<?php

namespace Tests\Feature\Public;

use App\Enums\BlogStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests that the public blog listing and single-post pages show the
 * estimated reading time (US-29 / T-29-4).
 */
class BlogReadTimeTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(): BlogPost
    {
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);
        $author = User::factory()->create();

        return BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'How to Import a Car',
            'slug' => 'how-to-import-a-car',
            'excerpt' => 'A short guide.',
            'body' => implode(' ', array_fill(0, 600, 'word')),
            'status' => BlogStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    #[Test]
    public function the_blog_listing_shows_the_estimated_reading_time(): void
    {
        $this->makePost();

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('3 min read');
    }

    #[Test]
    public function a_blog_post_shows_the_estimated_reading_time(): void
    {
        $post = $this->makePost();

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('3 min read');
    }
}
