<?php

/**
 * Regenerates public/sitemap.xml from the cars, blog posts, and static
 * pages that should be discoverable by search engines.
 *
 * @author Ohene Adjei
 */

namespace App\Console\Commands;

use App\Enums\CarStatus;
use App\Models\BlogPost;
use App\Models\Car;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Regenerate public/sitemap.xml with cars, blog posts, and static pages.';

    public function handle(): void
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home'))->setPriority(1.0))
            ->add(Url::create(route('cars.index'))->setPriority(0.9))
            ->add(Url::create(route('blog.index'))->setPriority(0.7))
            ->add(Url::create(route('about'))->setPriority(0.5))
            ->add(Url::create(route('contact'))->setPriority(0.5))
            ->add(Url::create(route('payment-info'))->setPriority(0.4));

        Car::where('status', CarStatus::Available)->get()->each(
            fn (Car $car) => $sitemap->add(
                Url::create(route('cars.show', $car->slug))
                    ->setLastModificationDate($car->updated_at)
                    ->setPriority(0.8)
            )
        );

        BlogPost::published()->get()->each(
            fn (BlogPost $post) => $sitemap->add(
                Url::create(route('blog.show', $post->slug))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.6)
            )
        );

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated at public/sitemap.xml.');
    }
}
