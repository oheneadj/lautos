<?php

namespace Tests\Unit\Models;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Order, User, and BlogPost all declare $recordRouteKeyName = 'uuid' on
 * their Filament resource, but that alone doesn't change what Filament's
 * getUrl() generates — it still falls back to the model's own
 * getRouteKeyName(), which defaults to the integer id unless the model
 * overrides it too. This locks in that all three actually do.
 */
class AdminRouteKeyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_order_admin_view_link_uses_the_uuid_not_the_integer_id(): void
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
        $order = Order::factory()->create(['car_id' => $car->id]);

        $url = OrderResource::getUrl('view', ['record' => $order]);

        $this->assertSame('/admin/orders/'.$order->uuid, parse_url($url, PHP_URL_PATH));
    }

    #[Test]
    public function the_customer_admin_view_link_uses_the_uuid_not_the_integer_id(): void
    {
        $user = User::factory()->create();

        $url = CustomerResource::getUrl('view', ['record' => $user]);

        $this->assertSame('/admin/customers/'.$user->uuid, parse_url($url, PHP_URL_PATH));
    }

    #[Test]
    public function the_blog_post_admin_edit_link_uses_the_uuid_not_the_integer_id(): void
    {
        $category = BlogCategory::create(['name' => 'Import Guides', 'slug' => 'import-guides']);
        $author = User::factory()->create();
        $post = BlogPost::create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'A Post',
            'excerpt' => 'An excerpt.',
            'body' => 'Body content.',
        ]);

        $url = BlogPostResource::getUrl('edit', ['record' => $post]);

        $this->assertSame('/admin/blog-posts/'.$post->uuid.'/edit', parse_url($url, PHP_URL_PATH));
    }
}
