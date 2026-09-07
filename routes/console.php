<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Refresh the homepage Instagram gallery from the Graph API every six
// hours. Requires the Laravel scheduler to be running via cron:
//   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
// Sync failures never break the site — the last synced posts stay visible.
Schedule::command('instagram:sync')->everySixHours()->withoutOverlapping();
Schedule::command('sellers:expire-subscriptions')->dailyAt('01:00')->withoutOverlapping();
