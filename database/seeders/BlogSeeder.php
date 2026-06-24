<?php

namespace Database\Seeders;

use App\Enums\BlogStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create an admin author
        $author = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Livingston Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // 1. Create Categories
        $categories = [
            'News' => BlogCategory::firstOrCreate(['slug' => 'news'], ['name' => 'News']),
            'Reviews' => BlogCategory::firstOrCreate(['slug' => 'reviews'], ['name' => 'Reviews']),
            'Buying Guides' => BlogCategory::firstOrCreate(['slug' => 'buying-guides'], ['name' => 'Buying Guides']),
            'Shipping Advice' => BlogCategory::firstOrCreate(['slug' => 'shipping-advice'], ['name' => 'Shipping Advice']),
        ];

        // 2. Define Blog Posts
        $posts = [
            [
                'title' => 'The Top 5 Most Reliable Used SUVs to Import from Japan in 2025',
                'category' => 'Reviews',
                'excerpt' => 'Looking for a reliable family hauler that won\'t break the bank? We review the top 5 Japanese SUVs that offer the best value and longevity for Ghanaian roads.',
                'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 1,
            ],
            [
                'title' => 'Understanding Ghana Port Customs Duties: A Complete Guide',
                'category' => 'Buying Guides',
                'excerpt' => 'Don\'t get caught off guard by hidden fees. This comprehensive guide breaks down how customs duties are calculated for imported vehicles at Tema and Takoradi ports.',
                'image' => 'https://images.unsplash.com/photo-1580226056345-3dbcb30a8c2c?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 2,
            ],
            [
                'title' => 'Hyundai Elantra vs Toyota Corolla: Which is the Better Import?',
                'category' => 'Reviews',
                'excerpt' => 'The eternal debate between the Korean and Japanese compact sedans. We compare specs, maintenance costs, and import prices to see which one you should buy.',
                'image' => 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 3,
            ],
            [
                'title' => 'Shipping from South Korea to Tema: What to Expect',
                'category' => 'Shipping Advice',
                'excerpt' => 'South Korea has become a major hub for high-quality used cars. Here is everything you need to know about RoRo shipping schedules, transit times, and documentation.',
                'image' => 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 5,
            ],
            [
                'title' => 'Why Left-Hand Drive Conversion is No Longer Necessary',
                'category' => 'Buying Guides',
                'excerpt' => 'A common misconception is that all Japanese cars are Right-Hand Drive. We explain how we source factory Left-Hand Drive cars directly from the Korean and Japanese markets.',
                'image' => 'https://images.unsplash.com/photo-1518081461904-9d8f13635102?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 6,
            ],
            [
                'title' => 'Livingston Autos Expands Network to Include Premium European Imports via Japan',
                'category' => 'News',
                'excerpt' => 'We are excited to announce new partnerships allowing us to source high-grade used Mercedes, BMW, and Audi vehicles from Japanese auctions.',
                'image' => 'https://images.unsplash.com/photo-1617469767053-d3b523a0b982?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 7,
            ],
            [
                'title' => 'How to Spot Mileage Rollbacks Before You Import',
                'category' => 'Buying Guides',
                'excerpt' => 'Odometer fraud is a serious issue in the used car market. Learn how our multi-point inspection process and auction sheet verifications protect you from buying a rolled-back vehicle.',
                'image' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 9,
            ],
            [
                'title' => '2025 Kia Sportage Review: Bold Design Meets Practicality',
                'category' => 'Reviews',
                'excerpt' => 'The redesigned Kia Sportage is turning heads. We dive into its performance, tech features, and why it is becoming one of our most requested imports.',
                'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 10,
            ],
            [
                'title' => 'Ghana Import Duty Changes for Hybrid and Electric Vehicles',
                'category' => 'News',
                'excerpt' => 'Recent policy updates offer potential tax breaks for importing eco-friendly vehicles. Here is what the new regulations mean for your next car purchase.',
                'image' => 'https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 12,
            ],
            [
                'title' => 'Container vs RoRo Shipping: Which is Best for Your Vehicle?',
                'category' => 'Shipping Advice',
                'excerpt' => 'Should you ship your car in a container or via Roll-on/Roll-off? We break down the costs, safety benefits, and transit times of both methods.',
                'image' => 'https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 14,
            ],
            [
                'title' => 'The Ultimate Checklist for Receiving Your Imported Car',
                'category' => 'Buying Guides',
                'excerpt' => 'Your car has finally arrived at Tema port. Follow this checklist to ensure a smooth handover and to verify the condition of your vehicle upon delivery.',
                'image' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 15,
            ],
            [
                'title' => 'Review: Why the Honda CR-V Remains the King of Compact SUVs',
                'category' => 'Reviews',
                'excerpt' => 'Year after year, the Honda CR-V proves its worth. We look at the top reasons why Ghanaian families continue to choose this reliable SUV.',
                'image' => 'https://images.unsplash.com/photo-1503376760367-7a54a72d73f1?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 18,
            ],
            [
                'title' => 'Tips for Buying a Used Car at Japanese Auctions',
                'category' => 'Buying Guides',
                'excerpt' => 'Decoding Japanese auction sheets can be tricky. We explain the grading system, interior marks, and how we bid to get you the best possible price.',
                'image' => 'https://images.unsplash.com/photo-1532581140115-3e355d1ed1de?auto=format&fit=crop&w=1200&q=80',
                'days_ago' => 20,
            ]
        ];

        foreach ($posts as $index => $data) {
            BlogPost::firstOrCreate(
                ['title' => $data['title']],
                [
                    'uuid' => (string) Str::uuid(),
                    'slug' => Str::slug($data['title']),
                    'blog_category_id' => $categories[$data['category']]->id,
                    'author_id' => $author->id,
                    'excerpt' => $data['excerpt'],
                    'body' => '<p>' . $data['excerpt'] . '</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>',
                    'cover_image_path' => $data['image'],
                    'status' => BlogStatus::Published,
                    'published_at' => Carbon::now()->subDays($data['days_ago'])->subHours(rand(1, 23)),
                    'created_at' => Carbon::now()->subDays($data['days_ago'])->subHours(rand(1, 23)),
                    'updated_at' => Carbon::now()->subDays($data['days_ago'])->subHours(rand(1, 23)),
                ]
            );
        }
    }
}
