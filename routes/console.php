<?php


use App\Jobs\RecomputeMetricsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::command(new \App\Console\Commands\RecomputeMetricsCommand())
    ->everyFifteenMinutes()
    ->onOneServer();

Schedule::job(new RecomputeMetricsJob())
    ->everyFiveMinutes()
    ->withoutOverlapping();



