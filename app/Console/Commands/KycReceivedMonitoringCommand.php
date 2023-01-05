<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KycReceivedMonitoringCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:kyc-received-monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive a notification if KYC received exceeds 11 hrs between Monday- Saturday';

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
        //Start Process Management 
        $process_manage = [
            'module'             => 'KYC Received',
            'description'        => 'Receive a notification if KYC received exceeds 11 hrs between Monday-Saturday',
            'command_name'       => 'mosh:kyc-received-monitor',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];
        //End Process Management

        $b2c_booking = DB::connection('b2cship')->select("SELECT TOP 1 AWBNO, CreatedDate
        FROM Packet ORDER BY CreatedDate DESC");

        $b2c_booking_awbNo = $b2c_booking[0]->AWBNO;

        $kyc_received = DB::connection('b2cship')->select("SELECT TOP 1 AWBNO, CreatedDate
        FROM Packet WHERE IsKYC ='true' ORDER BY CreatedDate DESC");

        $kyc_received_awbNo = $kyc_received[0]->AWBNO;
        $kyc_received_date = Carbon::parse($kyc_received[0]->CreatedDate);
        $dayName = $kyc_received_date->dayName;

        $getTime = Carbon::parse($kyc_received[0]->CreatedDate);
        $now = Carbon::now();
        $timeDiff = $getTime->diff($now);
        if ($b2c_booking_awbNo != $kyc_received_awbNo) {

            if ($dayName != 'Sunday' && $timeDiff->h >= 11) {

                slack_notification('monitor', 'KYC Received', 'KYC received exceeds 11 hours');
            }
        }

        //Start Update Process Management
        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
        //End Update Process Management
    }
}
