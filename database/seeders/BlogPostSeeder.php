<?php

/**
 * Seeds a starter blog category and post so the public blog and the catalogue's
 * "Latest News" section have real content to show.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use App\Enums\BlogStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $category = BlogCategory::firstOrCreate(
            ['slug' => 'import-guides'],
            ['name' => 'Import Guides']
        );

        $author = User::where('is_admin', true)->first() ?? User::first();

        BlogPost::firstOrCreate(
            ['slug' => 'how-to-import-a-car-to-ghana'],
            [
                'blog_category_id' => $category->id,
                'author_id' => $author->id,
                'title' => 'How to Import a Car to Ghana: A Step-by-Step Guide',
                'excerpt' => 'From choosing the right vehicle abroad to clearing it at Tema port, here is what to expect when importing a car through Livingston Autos.',
                'body' => <<<'BODY'
Importing a car to Ghana involves a few key stages: selecting the vehicle, shipping it from the country of origin, clearing it through customs, and final delivery.

At Livingston Autos, we handle the sourcing and shipping for you. Once you place an order, we confirm payment, purchase the vehicle, and begin the shipping process to Tema port. You can track every stage from your dashboard.

Customs clearance is usually the part that surprises first-time buyers the most. Duties are calculated based on the vehicle's age, engine size, and declared value, and delays at port can add demurrage charges if clearance documents aren't ready in time. We flag this risk on every order so there are no surprises.

Once cleared, your car is delivered to your chosen location in Ghana, fully registered and ready to drive.
BODY,
                'status' => BlogStatus::Published,
                'published_at' => now()->subDays(2),
            ]
        );
    }
}
