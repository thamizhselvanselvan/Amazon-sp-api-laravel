<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use League\Csv\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class B2cshipKycController extends Controller
{
    public function index()
    {


        $path = 'B2cship_kyc\B2cship_kyc.json';

        if (!Storage::exists($path)) {
            $b2cship_array = [

                'totalBooking' => NULL,
                'kycApproved' => NULL,
                'kycPending' => NULL,
                'kycRejected' => NULL
            ];
            $todayTotalBooking = $b2cship_array;
            $yesterdayTotalBooking = $b2cship_array;
            $Last7DaysTotalBooking = $b2cship_array;
            $Last30DaysTotalBooking = $b2cship_array;

            return view('b2cship.kyc.index', compact(['todayTotalBooking', 'yesterdayTotalBooking', 'Last7DaysTotalBooking', 'Last30DaysTotalBooking']));
        }

        $startTime = Carbon::today();
        $endTime = Carbon::now();
        $todayTotalBooking =  $this->kycDetails($startTime, $endTime);

        $file_path = Storage::get($path);
        $jsonFile = json_decode($file_path);

        $yesterdayTotalBooking = (array)$jsonFile[0];
        $Last7DaysTotalBooking = (array)$jsonFile[1];
        $Last30DaysTotalBooking = (array)$jsonFile[2];


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

            $kycStatus = DB::connection('mssql')->select("SELECT DISTINCT AwbNo, IsRejected FROM KYCStatus WHERE AwbNo IN ($awb) AND ModifiedDate BETWEEN '$start' AND '$end' ");

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
