<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (env('SCHEDULER_ENABLED', true)) {
    Schedule::command('notify:payment-reminder')
        ->dailyAt('08:00')
        ->timezone('Asia/Jakarta')
        ->withoutOverlapping()
        ->runInBackground()
        ->appendOutputTo(storage_path('logs/payment-reminder.log'));

    Schedule::command('invoices:update-overdue')
        ->dailyAt('00:05')
        ->timezone('Asia/Jakarta')
        ->withoutOverlapping();
}
