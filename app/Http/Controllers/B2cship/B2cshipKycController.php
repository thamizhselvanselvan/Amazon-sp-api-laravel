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
        $date = 0;

        $startTime = Carbon::today();
        $endTime = Carbon::now();

        $date = $startTime->toDateString();

        $todayTotalBooking =  $this->kycDetails($startTime, $endTime);
        // po($todayTotalBooking);
        // exit;
        return view('b2cship.kyc.index', compact('todayTotalBooking'));
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
