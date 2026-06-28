<?php

use App\Console\Commands\ArchiveSoldCars;
use App\Console\Commands\GenerateSitemap;
use App\Console\Commands\PublishScheduledPosts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(PublishScheduledPosts::class)->everyMinute();

// Ghana doesn't observe DST, so Africa/Accra is always UTC+0 — this runs at midnight Ghana time.
Schedule::command(ArchiveSoldCars::class)->dailyAt('00:00')->timezone('Africa/Accra');

// I regenerate the sitemap hourly rather than on every car/post save — search engines
// don't need it that fresh, and this avoids regenerating on every single admin edit.
Schedule::command(GenerateSitemap::class)->hourly();

// I back up nightly rather than more often — this app's data (cars, orders, KYC
// docs) doesn't change fast enough to justify the extra disk/CPU on shared hosting.
Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at('01:30');
Schedule::command('backup:monitor')->daily()->at('02:00');
