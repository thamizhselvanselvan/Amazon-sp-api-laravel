<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BombinoPacketActivitie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:bombino-packet-activities {month} {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create json file according to month for bombino packet activities details';

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
        $month = $this->argument('month');
        $year = $this->argument('year');
        $year_array = [
            '1' => 'jan',
            '2' => 'feb',
            '3' => 'mar',
            '4' => 'apr',
            '5' => 'may',
            '6' => 'Jun',
            '7' => 'Jul',
            '8' => 'aug',
            '9' => 'sep',
            '10' => 'oct',
            '11' => 'nov',
            '12' => 'dec'
        ];
        $today_sd = Carbon::today();
        $today_ed = Carbon::now();
        $current_month = $today_ed->format('m');
        $month_name = $year_array[$month];

        $start = Carbon::create()->month($month)->startOfMonth()->format('m-d');
        $end = Carbon::create()->month($month)->endOfMonth()->format('m-d');

        if ($month == $current_month) {

            $yesterday_sd = Carbon::yesterday();
            $yesterday_ed = $yesterday_sd->toDateString();
            $yesterday_ed = $yesterday_ed . ' 23:59:59';

            $start_date = $year . '-' . $start . ' 00:00:00';
            $end_date = $yesterday_ed;
        } elseif ($month == 2) {
            $start_date = $year . '-' . $start . ' 00:00:00';
            if ($year / 4 == 0) {
                $end_date = $year . '-02-29' . ' 23:59:59';
            } else {

                $end_date = $year . '-02-28' . ' 23:59:59';
            }
        } else {

            $start_date = $year . '-' . $start . ' 00:00:00';
            $end_date = $year . '-' . $end . ' 23:59:59';
        }

        $packet_detials = DB::connection('b2cship')->select("SELECT DISTINCT
        AwbNo, PODLocation, StatusDetails,FPCode,CreatedDate
        from PODTrans
        WHERE CreatedDate BETWEEN convert(datetime, '$start_date') AND convert(datetime,'$end_date')
        AND FPCode ='BOMBINO'
        ORDER BY CreatedDate DESC");

        $file_path = 'Bombino/bombino_' . $month_name . '_' . $year . '.json';
        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        Storage::put($file_path, json_encode($packet_detials));
    }
}
