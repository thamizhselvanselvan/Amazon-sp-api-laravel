<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BombinoPacketActivitie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:bombino-packet-activities {month}';

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
        $start_date ='';
        $end_date = '';

        $packet_detials = DB::connection('mssql')->select("SELECT DISTINCT
        AwbNo, PODLocation, StatusDetails,FPCode,CreatedDate 
        from PODTrans
        WHERE CreatedDate BETWEEN '$start_date' AND '$end_date'
        AND FPCode ='BOMBINO' 
        ORDER BY CreatedDate DESC");


    }
}
