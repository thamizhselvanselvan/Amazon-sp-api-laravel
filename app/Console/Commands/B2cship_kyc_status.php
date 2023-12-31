<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use League\Csv\Reader;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class B2cship_kyc_status extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:b2cship-kyc-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'B2cship kyc status';

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
        //Process Management start
        $process_manage = [
            'module'             => 'B2CShip',
            'description'        => 'B2cship kyc status Dashboard',
            'command_name'       => 'pms:b2cship-kyc-status',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $startTime = Carbon::today();
        $endTime = Carbon::now();

        $startTime = Carbon::yesterday();
        $endTimeYesterday = $startTime->toDateString();
        $endTimeYesterday = $endTimeYesterday . ' 23:59:59';
        $yesterdayTotalBooking =  $this->kycDetails($startTime, $endTimeYesterday);

        $startTime = Carbon::today()->subDays(7);
        $Last7DaysTotalBooking =  $this->kycDetails($startTime, $endTime);

        $startTime = Carbon::today()->subDays(30);
        $Last30DaysTotalBooking =  $this->kycDetails($startTime, $endTime);

        // PUT DATA IN JSON FILE

        $arr = array($yesterdayTotalBooking, $Last7DaysTotalBooking, $Last30DaysTotalBooking);
        $result['b2cship_kyc'] = json_encode($arr);

        Storage::disk('local')->put('B2cship_kyc/B2cship_kyc.json', $result);


        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }


    public function kycDetails($start, $end)
    {
        $totalBookingArray = [];
        $totalBookingCount = 0;
        $totalkycApprovedCount = 0;
        $totalkycRejectedCount = 0;
        $totalkycPendingCount = 0;

        $totalBookings = DB::connection('b2cship')->select("SELECT AwbNo FROM Packet WHERE CreatedDate BETWEEN '$start' AND '$end'");

        if (count($totalBookings)) {

            foreach ($totalBookings as $totalBooking) {
                foreach ($totalBooking as $totalBookingAWB) {
                    $totalBookingArray[] = "'$totalBookingAWB'";
                }
            }
            $awb = implode(',', $totalBookingArray);
            $awb = ltrim($awb);

            $kycStatus = DB::connection('b2cship')->select("SELECT DISTINCT AwbNo, IsRejected FROM KYCStatus WHERE AwbNo IN ($awb) AND ModifiedDate BETWEEN '$start' AND '$end' ");

            $kycApproved = [];
            $kycApprovedOffset = 0;
            $kycRejected = [];
            $kycRejectedOffset = 0;

            foreach ($kycStatus as $kyc) {
                if ($kyc->IsRejected == '0') {
                    $kycApproved[$kycApprovedOffset] = $kyc;
                    $kycApprovedOffset++;
                } else {

                    $kycRejected[$kycRejectedOffset] = $kyc;
                    $kycRejectedOffset++;
                }
            }

            $totalBookingCount = count($totalBookingArray);
            $totalkycApprovedCount = count($kycApproved);
            $totalkycRejectedCount = count($kycRejected);
            $totalkycPendingCount = $totalBookingCount - ($totalkycApprovedCount + $totalkycRejectedCount);
            $totalkycPendingCount = $totalkycPendingCount < 0 ? 0 : $totalkycPendingCount;

            $finalArray['totalBooking'] = $totalBookingCount;
            $finalArray['kycApproved'] = $totalkycApprovedCount;
            $finalArray['kycRejected'] = $totalkycRejectedCount;
            $finalArray['kycPending'] = $totalkycPendingCount;

            return ($finalArray);
        } else {

            $finalArray['totalBooking'] = $totalBookingCount;
            $finalArray['kycApproved'] = $totalkycApprovedCount;
            $finalArray['kycRejected'] = $totalkycRejectedCount;
            $finalArray['kycPending'] = $totalkycPendingCount;

            return ($finalArray);
        }
    }
}
