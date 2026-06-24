<?php

namespace Tests\Unit\Models;

use App\Enums\BlogStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the BlogPost model's reading-time estimate (US-29).
 */
class BlogPostTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(string $body): BlogPost
    {
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);
        $author = User::factory()->create();

        return BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'A Test Post',
            'excerpt' => 'An excerpt.',
            'body' => $body,
            'status' => BlogStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    #[Test]
    public function it_estimates_reading_time_at_200_words_per_minute(): void
    {
        $post = $this->makePost(implode(' ', array_fill(0, 400, 'word')));

        $this->assertSame(2, $post->read_time);
    }

    #[Test]
    public function it_rounds_up_a_partial_minute(): void
    {
        $post = $this->makePost(implode(' ', array_fill(0, 201, 'word')));

        $this->assertSame(2, $post->read_time);
    }

    #[Test]
    public function it_never_reports_less_than_one_minute(): void
    {
        $post = $this->makePost('Just a few words here.');

        $this->assertSame(1, $post->read_time);
    }

    #[Test]
    public function it_strips_html_tags_before_counting_words(): void
    {
        $post = $this->makePost('<p>'.implode(' ', array_fill(0, 400, 'word')).'</p>');

        $this->assertSame(2, $post->read_time);
    }
}
