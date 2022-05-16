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
        $schedule->command('pms:textiles-import')->everyFourHours()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');;
        $schedule->command('pms:microstatus-report')->dailyAt('1:00')->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        
        $schedule->command('pms:b2cship_kyc:status')->dailyAt('1:00');
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
