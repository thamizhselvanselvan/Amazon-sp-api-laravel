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

        // $datas =  Backup::where("status", 1)->get(["connection", "table_name"])->groupBy("connection");

        // foreach ($datas as $connection => $table_names) {

        //     $table_names = collect($table_names)->pluck("table_name");

        //     Config::set(
        //         "database.connections.{$connection}.dump.excludeTables",
        //         $table_names
        //     );
        
        // }
    }
}
