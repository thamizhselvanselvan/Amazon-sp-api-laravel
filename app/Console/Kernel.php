<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // $schedule->command('pms:seller-order-item-import')->everyTenMinutes();

        if (app()->environment() === 'production') {

            if (Cache::has('Schedule_command')) {
                $commands = Cache::get('Schedule_command')->toArray();

                foreach ($commands as $command) {
                    $schedule->command($command['command_name'])->cron($command['execution_time']);
                }
            } else {
                CacheForCommandScheduler();
            }

            // $schedule->command('mosh:product_fetch')->everyTenMinutes();

            //bb_stores
            // $schedule->command('mosh:price_priority_import')->everyMinute(); //PM

            /*Inventory*/
            // $schedule->command('pms:inventory-stock-tracking')->dailyAt('23:45'); //PM
            // $schedule->command('mosh:warehouse-track')->dailyAt('23:50');  //PM
            // $schedule->command('mosh:tag-track')->dailyAt('23:55'); //PM

            /*Business API*/
            // $schedule->command('mosh:access_token_generate')->cron('*/55 * * * *');
            // $schedule->command('mosh:zoho:generate_token')->cron('*/55 * * * *');

            /*B2CShip*/
            // $schedule->command('pms:b2cship-microstatus-report')->daily(); //PM
            // $schedule->command('pms:b2cship-kyc-status')->daily(); //PM
            // $schedule->command('mosh:kyc-received-monitor')->hourly();

            /*BEO*/
            // $schedule->command('pms:boe-upload-Do')->everyFourHours(); //PM
            // $schedule->command('pms:remove-uploaded-boe')->dailyAt('01:00'); //PM

            /*Catalog*/
            // $schedule->command('mosh:catalog-amazon-import')->everyTwoMinutes(); //PM
            // $schedule->command('mosh:Catalog-price-import-bb-in')->everyTwoMinutes(); //PM
            // $schedule->command('mosh:Catalog-price-import-bb-us')->everyTwoMinutes(); //PM
            // $schedule->command('mosh:Catalog-price-import-bb-ae')->everyMinute(); //PM
            // $schedule->command('mosh:catalog-dashboard-file')->everyThirtyMinutes(); //PM

            /*Orders*/
            // $schedule->command('pms:sellers-orders-import')->everyTenMinutes(); //PM
            // $schedule->command('mosh:order-item-details-import')->everyMinute(); //PM
            // $schedule->command('mosh:check-store-creds')->everyFourHours(); //PM

            /*Misc*/
            // $schedule->command('backup:run')->twiceDaily();
            // $schedule->command('backup:clean')->daily()->at('01:00');

            /*AWS Sync - Needs to be removed*/
            // $schedule->command('aws:nitshop:order')->hourly(); //PM
            // $schedule->command('aws:nitshop:order_details')->hourly(); //PM

            /*Order CI CD*/
            // $schedule->command('aws:courier-booking')->everyMinute();
            // $schedule->command('mosh:feed-app360-tracking-details')->everyMinute();
            // $schedule->command('mosh:feed-status')->everyTwoMinutes();
            // $schedule->command('mosh:zoho:save')->everyMinute();
        }

        if (app()->environment() === 'staging') {

            /*Order CI CD*/
            /*
                Zoho - only one record at a time for staging
            */
            // $schedule->command('mosh:access_token_generate')->cron('*/55 * * * *');
            if (Cache::has('Schedule_command')) {

                $commands = Cache::get('Schedule_command')->toArray();
                foreach ($commands as $command) {
                    $schedule->command($command['command_name'])->cron($command['execution_time']);
                }
            } else {
                CacheForCommandScheduler();
            }
        }

        if (app()->environment() === 'local') {
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
