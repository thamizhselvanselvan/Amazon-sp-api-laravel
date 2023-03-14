<?php

namespace App\Providers;

use App\Models\Admin\Backup;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->environment() === 'production') {
            //DB::statement('SET SESSION sql_require_primary_key=0');
        }
    }
}
