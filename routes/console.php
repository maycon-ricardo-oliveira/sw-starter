<?php


use Illuminate\Support\Facades\Schedule;

Schedule::command('metrics:recompute')
    ->everyFifteenMinutes()
    ->onOneServer();


