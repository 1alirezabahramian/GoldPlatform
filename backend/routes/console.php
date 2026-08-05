<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (config('outbox.dispatch_enabled', false)) {
    Schedule::command('outbox:dispatch --fail-on-error')
        ->everyMinute()
        ->withoutOverlapping(5)
        ->onOneServer();
}
