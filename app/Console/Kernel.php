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
        // $schedule->command('pms:textiles-import')->everyFourHours()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');;
        $schedule->command('pms:microstatus-report')->daily()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');

        $schedule->command('pms:b2cship-kyc-status')->daily()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        // $schedule->command('pms:seller-order-item-import')->everyTenMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        $schedule->command('pms:stock-tracking')->dailyAt('23:00')->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        $schedule->command('mosh:warehouse-track')->dailyAt('23:00')->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        $schedule->command('mosh:tag-track')->dailyAt('23:00')->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        $schedule->command('mosh:access_token_generate')->cron('*/50 * * * *')->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');

        if (app()->environment() === 'production') {

            $schedule->command('backup:run')->twiceDaily();
            $schedule->command('backup:clean')->daily()->at('01:00');
            $schedule->command('pms:boe-upload-Do')->everyFourHours()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            $schedule->command('pms:remove-uploaded-boe')->daily()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            $schedule->command('pms:sellers-orders-import')->everyTenMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            $schedule->command('mosh:order-item-details-import')->everyMinute()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');

            $schedule->command('mosh:catalog-amazon-import')->everyFiveMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            $schedule->command('mosh:Catalog-price-import-bb-in')->everyThreeMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            $schedule->command('mosh:Catalog-price-import-bb-us')->everyMinute()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            $schedule->command('mosh:catalog-dashboard-file')->hourly()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            $schedule->command('aws:nitshop:order')->hourly();
            $schedule->command('aws:nitshop:order_details')->hourly();

            $schedule->command('mosh:feed-tracking-details-to-amazon')->everyMinute()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            //$schedule->command('mosh:seller-asin-get-pricing')->daily()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        }

        if (app()->environment() === 'staging') {
            // $schedule->command('pms:sellers-orders-import')->everyTenMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');

            // $schedule->command('mosh:catalog-amazon-import')->everyFiveMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            // $schedule->command('mosh:order_cliqnshop_place')->everyMinute()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            //$schedule->command('mosh:catalog-dashboard-file')->everyFiveMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            // $schedule->command('mosh:Catalog-price-import-bb-in')->everyThreeMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
            // $schedule->command('mosh:Catalog-price-import-bb-us')->everyMinute()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
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
