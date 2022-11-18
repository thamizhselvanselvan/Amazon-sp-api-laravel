<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if ('staging' === App::environment() || 'production' === App::environment()) {

    Artisan::command('migrate:fresh', function () {
        $this->comment('You are not allowed to do this in production!');
    })->describe('Overri4de default command in production.');

    Artisan::command('migrate:rollback', function () {
        $this->comment('You are not allowed to do this in production!');
    })->describe('Override default command in production.');

    Artisan::command('migrate:install', function () {
        $this->comment('You are not allowed to do this in production!');
    })->describe('Override default command in production.');

    Artisan::command('migrate:refresh', function () {
        $this->comment('You are not allowed to do this in production!');
    })->describe('Override default command in production.');

    Artisan::command('migrate:reset', function () {
        $this->comment('You are not allowed to do this in production!');
    })->describe('Override default command in production.');
}
