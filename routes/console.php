<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily database backup (needs the scheduler: php artisan schedule:work, or a cron / Task Scheduler entry)
Schedule::command('shop:backup')
    ->dailyAt((string) config('logistics.backup.time', '03:00'))
    ->withoutOverlapping();

// Abandoned cart reminder SMS (needs the scheduler, same as the backup above)
Schedule::command('shop:abandoned-reminders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

// Heartbeat: lets `php artisan shop:launch-check` prove that the server cron is really running
Schedule::call(fn () => Cache::put('scheduler:heartbeat', now()->timestamp, 3600))
    ->everyMinute()
    ->name('scheduler-heartbeat');
