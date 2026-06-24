<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\BlogCategories\Pages\ListBlogCategories;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests the admin blog category resource — previously categories could
 * only be created inline from the Blog Post form, with no standalone
 * list to rename or delete them.
 */
class BlogCategoryManagementTest extends TestCase
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
    public function guest_cannot_access_blog_category_management(): void
    {
        $this->get('/admin/blog-categories')->assertRedirect('/admin/login');
    }

    #[Test]
    public function admin_can_create_a_blog_category(): void
    {
        $this->actingAsAdmin();

        Livewire::test(ListBlogCategories::class)
            ->callAction('create', data: [
                'name' => 'Import Tips',
                'slug' => 'import-tips',
            ]);

        $this->assertDatabaseHas('blog_categories', [
            'name' => 'Import Tips',
            'slug' => 'import-tips',
        ]);
    }

    #[Test]
    public function admin_can_see_categories_with_post_counts(): void
    {
        $this->actingAsAdmin();

        $category = BlogCategory::create(['name' => 'Industry News', 'slug' => 'industry-news']);

        Livewire::test(ListBlogCategories::class)
            ->assertCanSeeTableRecords([$category])
            ->assertSee('Industry News');
    }

    #[Test]
    public function admin_can_rename_a_category(): void
    {
        $this->actingAsAdmin();

        $category = BlogCategory::create(['name' => 'Old Name', 'slug' => 'old-name']);

        Livewire::test(ListBlogCategories::class)
            ->callTableAction('edit', $category, data: [
                'name' => 'New Name',
                'slug' => 'new-name',
            ]);

        $this->assertSame('New Name', $category->refresh()->name);
    }

    #[Test]
    public function admin_can_delete_a_category(): void
    {
        $this->actingAsAdmin();

        $category = BlogCategory::create(['name' => 'Temp', 'slug' => 'temp']);

        Livewire::test(ListBlogCategories::class)
            ->callTableAction('delete', $category);

        $this->assertDatabaseMissing('blog_categories', ['id' => $category->id]);
    }
}
