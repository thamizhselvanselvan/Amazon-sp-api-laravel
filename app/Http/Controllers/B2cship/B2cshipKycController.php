<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class B2cshipKycController extends Controller
{
    public function index()
    {

        $startTime = Carbon::today();
        $endTime = Carbon::now();

        $todayTotalBooking =  $this->kycDetails($startTime, $endTime);


        $startTime = Carbon::yesterday();
        $endTimeYesterday = $startTime->toDateString();
        $endTimeYesterday = $endTimeYesterday . ' 23:59:59';
        $yesterdayTotalBooking =  $this->kycDetails($startTime, $endTimeYesterday);


        $startTime = Carbon::today()->subDays(7);
        $Last7DaysTotalBooking =  $this->kycDetails($startTime, $endTime);


        $startTime = Carbon::today()->subDays(30);
        $Last30DaysTotalBooking =  $this->kycDetails($startTime, $endTime);

        return view('b2cship.kyc.index', compact(['todayTotalBooking', 'yesterdayTotalBooking', 'Last7DaysTotalBooking', 'Last30DaysTotalBooking']));
    }

    public function kycDetails($start, $end)
    {
        $totalBookingArray = [];
        $totalBookingCount = 0;
        $totalkycApprovedCount = 0;
        $totalkycRejectedCount = 0;
        $totalkycPendingCount = 0;

        $totalBookings = DB::connection('mssql')->select("SELECT AwbNo FROM Packet WHERE CreatedDate BETWEEN '$start' AND '$end'");

        if (count($totalBookings)) {

            foreach ($totalBookings as $totalBooking) {
                foreach ($totalBooking as $totalBookingAWB) {
                    $totalBookingArray[] = "'$totalBookingAWB'";
                }
            }


            $awb = implode(',', $totalBookingArray);
            $awb = ltrim($awb);

            $kycApproved = DB::connection('mssql')->select("SELECT DISTINCT AwbNo FROM KYCStatus WHERE AwbNo IN ($awb) AND IsRejected = '0' ");

            $kycRejected = DB::connection('mssql')->select("SELECT DISTINCT AwbNo FROM KYCStatus WHERE AwbNo IN ($awb) AND IsRejected = '1' ");

            $totalBookingCount = count($totalBookingArray);
            $totalkycApprovedCount = count($kycApproved);
            $totalkycRejectedCount = count($kycRejected);
            $totalkycPendingCount = $totalBookingCount - ($totalkycApprovedCount + $totalkycRejectedCount);

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
