<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Jibble data sync twice daily
Schedule::command('jibble:sync --all')
    ->twiceDaily(8, 18) // Run at 8:00 AM and 6:00 PM
    ->timezone('Asia/Kathmandu') // Adjust to your timezone
    ->withoutOverlapping()
    ->runInBackground();
