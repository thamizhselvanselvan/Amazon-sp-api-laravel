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

        $startTime = Carbon::today();
        $endTime = Carbon::now();

        // $todayTotalBooking =  $this->kycDetails($startTime, $endTime);

         $path='D:\laragon\www\amazon-sp-api-laravel\storage\app\B2cship_kyc\B2cship_kyc.json';

        // if(!Storage::exists($path))
        // {
        //     return "file not found";
        // }

        // $path=Reader::createFromPath('D:\laragon\www\amazon-sp-api-laravel\storage\app\B2cship_kyc\B2cship_kyc.json','r');
            $json=file_get_contents($path,true);
            // echo $json;
        
             $jsonFile=json_decode($json);
             
             $todayTotalBooking=(array)$jsonFile[0];
             $yesterdayTotalBooking=(array)$jsonFile[1];
             $Last7DaysTotalBooking=(array)$jsonFile[2];
             $Last30DaysTotalBooking=(array)$jsonFile[3];

       
        return view('b2cship.kyc.index', compact(['todayTotalBooking', 'yesterdayTotalBooking', 'Last7DaysTotalBooking', 'Last30DaysTotalBooking']));
    }

}
