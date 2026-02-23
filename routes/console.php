<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncHotspotActiveUsers;
use App\Jobs\SyncPppoeActiveSessions;
use App\Jobs\SyncDashboardStats;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sync Hotspot & PPPoE & Dashboard roughly every 5-10 seconds
// Since Laravel scheduler runs every minute, we use a loop to simulate sub-minute scheduling
Schedule::call(function () {
    for ($i = 0; $i < 6; $i++) {
        dispatch(new SyncHotspotActiveUsers());
        dispatch(new SyncPppoeActiveSessions());
        dispatch(new SyncDashboardStats()); // Run every 10s (approx)
        
        sleep(10);
    }
})->everyMinute();

// Automate Blog Generation at 1 AM and 2 PM (WIB)
Schedule::command('blog:auto-generate')->dailyAt('01:00');
Schedule::command('blog:auto-generate')->dailyAt('14:00');

// Clean up expired sessions daily at 4 AM to keep database lean
Schedule::call(function () {
    $expiration = now()->subMinutes(config('session.lifetime'))->getTimestamp();
    \DB::table('sessions')->where('last_activity', '<', $expiration)->delete();
})->dailyAt('04:00');
