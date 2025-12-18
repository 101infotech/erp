<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jibble sync disabled for HRM module; attendance remains manual/internal
// Schedule::command('jibble:sync --all')
//     ->twiceDaily(8, 18)
//     ->timezone('Asia/Kathmandu')
//     ->withoutOverlapping()
//     ->runInBackground();
