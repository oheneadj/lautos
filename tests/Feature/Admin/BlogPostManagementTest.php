<?php

namespace Tests\Feature\Admin;

use App\Enums\BlogStatus;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin blog post list's status tabs (Draft/Scheduled/Published).
 */
class BlogPostManagementTest extends TestCase
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

    private function makePost(array $attributes = []): BlogPost
    {
        $category = BlogCategory::firstOrCreate(['slug' => 'import-guides'], ['name' => 'Import Guides']);
        $author = User::factory()->create();

        return BlogPost::create(array_merge([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'A Test Post',
            'excerpt' => 'An excerpt.',
            'body' => 'Body content.',
            'status' => BlogStatus::Draft,
        ], $attributes));
    }

    #[Test]
    public function guest_cannot_access_blog_post_management(): void
    {
        $this->get('/admin/blog-posts')->assertRedirect('/admin/login');
    }

    #[Test]
    public function status_tabs_filter_the_table_to_just_that_status(): void
    {
        $this->actingAsAdmin();

        $draft = $this->makePost(['title' => 'Draft Post', 'status' => BlogStatus::Draft]);
        $published = $this->makePost(['title' => 'Published Post', 'status' => BlogStatus::Published, 'published_at' => now()->subDay()]);

        Livewire::test(ListBlogPosts::class)
            ->set('activeTab', BlogStatus::Published->value)
            ->assertCanSeeTableRecords([$published])
            ->assertCanNotSeeTableRecords([$draft]);
    }
}
