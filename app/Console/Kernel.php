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
        $schedule->command('pms:microstatus-report')->daily()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        
        $schedule->command('pms:b2cship-kyc-status')->daily()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        $schedule->command('pms:boe-upload-Do')->everyFourHours()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        $schedule->command('pms:remove-uploaded-boe')->daily()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        // $schedule->command('pms:sellers-orders-import')->everyTenMinutes()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
        $schedule->command('pms:seller-order-item-import')->everyTwoHours()->thenPing('http://beats.envoyer.io/heartbeat/uoR2oSENfKrIC4z');
    
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
