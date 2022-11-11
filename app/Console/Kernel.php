<?php

namespace App\Console;

use Carbon\Carbon;
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

            /*Inventory*/
            $schedule->command('pms:inventory-stock-tracking')->dailyAt('23:45');
            $schedule->command('mosh:warehouse-track')->dailyAt('23:50');
            $schedule->command('mosh:tag-track')->dailyAt('23:55');

            /*Business API*/
            $schedule->command('mosh:access_token_generate')->cron('*/55 * * * *');

            /*B2CShip*/
            $schedule->command('pms:b2cship-microstatus-report')->daily();
            $schedule->command('pms:b2cship-kyc-status')->daily();

            /*BEO*/
            $schedule->command('pms:boe-upload-Do')->everyFourHours();
            $schedule->command('pms:remove-uploaded-boe')->dailyAt('01:00');

            /*Catalog*/
            $schedule->command('mosh:catalog-amazon-import')->everyFiveMinutes();
            $schedule->command('mosh:Catalog-price-import-bb-in')->everyThreeMinutes();
            $schedule->command('mosh:Catalog-price-import-bb-us')->everyMinute();
            $schedule->command('mosh:catalog-dashboard-file')->hourly();

            /*Orders*/
            $schedule->command('pms:sellers-orders-import')->everyTenMinutes();
            $schedule->command('mosh:order-item-details-import')->everyMinute();

            /*Misc*/
            $schedule->command('backup:run')->twiceDaily();
            $schedule->command('backup:clean')->daily()->at('01:00');

            /*AWS Sync - Needs to be removed*/
            $schedule->command('aws:nitshop:order')->hourly();
            $schedule->command('aws:nitshop:order_details')->hourly();
            //$schedule->command('mosh:feed-tracking-details-to-amazon')->everyMinute();

            /*Order CI CD*/
            $schedule->command('aws:courier-booking')->everyMinute();
            $schedule->command('mosh:feed-app360-tracking-details')->everyMinute();
        }

        if (app()->environment() === 'staging') {

            /*Order CI CD*/
            /*
                B2C - only one order at a time for staging
                Zoho - only one record at a time for staging
                Amazon Feed but this is only for prod
            */
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
