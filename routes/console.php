<?php

use App\Console\Commands\ArchiveSoldCars;
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
