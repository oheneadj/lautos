<?php

namespace Tests\Feature\Admin;

use App\Enums\BlogStatus;
use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests slug and excerpt auto-generation on the admin blog post form.
 */
class BlogPostFormTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $this->seed(\Database\Seeders\ShieldPermissionsSeeder::class);

        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));

        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function the_slug_is_generated_from_the_title_when_left_blank(): void
    {
        $this->actingAsAdmin();
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);

        Livewire::test(CreateBlogPost::class)
            ->set('data.title', 'How to Clear Customs Fast')
            ->set('data.blog_category_id', $category->id)
            ->set('data.body', '<p>Some body content for the post.</p>')
            ->call('create')
            ->assertHasNoFormErrors();

        $post = BlogPost::latest('id')->first();

        $this->assertSame('how-to-clear-customs-fast', $post->slug);
    }

    #[Test]
    public function the_excerpt_is_generated_from_the_body_and_capped_at_290_characters(): void
    {
        $this->actingAsAdmin();
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);

        $longBody = '<p>'.str_repeat('Lorem ipsum dolor sit amet. ', 20).'</p>';

        Livewire::test(CreateBlogPost::class)
            ->set('data.title', 'A Long Post')
            ->set('data.blog_category_id', $category->id)
            ->set('data.body', $longBody)
            ->call('create')
            ->assertHasNoFormErrors();

        $post = BlogPost::latest('id')->first();

        $this->assertNotEmpty($post->excerpt);
        $this->assertLessThanOrEqual(290, strlen($post->excerpt));
        $this->assertStringNotContainsString('<p>', $post->excerpt);
    }

    #[Test]
    public function the_slug_regenerates_when_the_title_changes_on_an_existing_post(): void
    {
        $this->actingAsAdmin();
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);
        $author = User::factory()->create();

        $post = BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'Original Title',
            'excerpt' => 'An excerpt.',
            'body' => '<p>Body content.</p>',
            'status' => BlogStatus::Draft,
        ]);

        $this->assertSame('original-title', $post->slug);

        Livewire::test(EditBlogPost::class, ['record' => $post->uuid])
            ->set('data.title', 'A Brand New Title')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('a-brand-new-title', $post->refresh()->slug);
    }

    #[Test]
    public function a_manually_entered_excerpt_is_not_overwritten(): void
    {
        $this->actingAsAdmin();
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);

        Livewire::test(CreateBlogPost::class)
            ->set('data.title', 'A Post')
            ->set('data.blog_category_id', $category->id)
            ->set('data.excerpt', 'My custom excerpt.')
            ->set('data.body', '<p>Different body content entirely.</p>')
            ->call('create')
            ->assertHasNoFormErrors();

        $post = BlogPost::latest('id')->first();

        $this->assertSame('My custom excerpt.', $post->excerpt);
    }
}
