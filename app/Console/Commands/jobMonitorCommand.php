<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class jobMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:job_monitor_daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Will send Notification of Jobs Failed today ..at 06:00PM ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $end = Carbon::now();
        $yesterday = Carbon::yesterday();
        $currentyesterday    =   $yesterday->toDateString();
        $start    =   $currentyesterday . ' 18:00:00';;


        $data = DB::connection('web')->table('failed_jobs')
            ->whereBetween('failed_at', [$start, $end])
            ->get();

        $count = count($data);
        $message = '';
        if ($count > 0) {
            $message = $count. ' ' . 'Jobs Failed Today';
        } else {
            $message = 'No Failed Jobs Today';
        }
        Log::channel('slack')->info($message);

    }
}
