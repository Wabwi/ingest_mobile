<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send daily reminders at 2:00 PM Nairobi Time for users who haven't logged anything today
Schedule::command('reminder:send')->dailyAt('14:00');
