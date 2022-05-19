<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class B2cshipDashboardController extends Controller
{
  public function Dashboard()
  {
    $status_detials = DB::connection('b2cship')->select("SELECT StatusDetails, AwbNo, CreatedDate,FPCode
        FROM (
              SELECT StatusDetails, AwbNo, CreatedDate, FPCode
                    , ROW_NUMBER() OVER(PARTITION BY FPCode ORDER BY CreatedDate desc)row_num
              FROM PODTrans Where FPCode != '' AND FPCode != 'BD Delhi' 
            ) sub
        WHERE row_num = 1");

    $status_detials_array = [];
    foreach ($status_detials as $key => $value) {
      $date = $value->CreatedDate;
      $date_time = $this->CarbonGetDateDiff($date);
      $status_detials_array[$key] = [

        'StatusDetials' => $value->StatusDetails . ' [' . $value->AwbNo . ']',
        'FPCode' => $value->FPCode,
        'day' => $date_time['Days'],
        'time' => $date_time['time'],

      ];
    }
    $bombino_status = $this->BombinoStatus();
    $delivery_status = $this->BlueDartAndDeliveryStatus();
    return view('b2cship.dashboard', compact(['status_detials_array', 'bombino_status', 'delivery_status']));
  }

  public function CarbonGetDateDiff($date)
  {
    $date_details_array = ['Year', 'Month', 'Day', 'Hour', 'Minute', 'Second'];

    $date = substr($date, 0, strpos($date, "."));
    $created = new Carbon($date);
    $now = Carbon::now();
    $differnce = $created->diff($now);
    $final_date = '';
    $count = 0;
    foreach ((array)$differnce as $key => $value) {
      if ($value != 0 && $count < 6 && $count > 2) {
        $final_date .= $value > 1 ? $value . ' ' . $date_details_array[$count] . 's,  ' : $value . ' ' . $date_details_array[$count] . ',  ';
      }
      $count++;
    }
    return  [
      'Days' => $differnce->days>1 ? $differnce->days.' Days': $differnce->days.' Day',
      'time' => rtrim($final_date, ' ,').' Before'
    ];
  }
  public function BookingAndKycStatusDetails()
  {
    // $kyc_status = DB::connection('b2cship')->select("SELECT Status, AwbNo,CreatedDate
    //     FROM (
    //           SELECT Status, AwbNo, CreatedDate
    //                 , ROW_NUMBER() OVER(PARTITION BY Status ORDER BY CreatedDate desc)row_num
    //           FROM KYCStatus
    //         ) sub
    //     WHERE row_num = 1");

    // po($kyc_status);
    // exit;


    exit;
  }

  public function BombinoStatus()
  {
    $kyc_status = DB::connection('b2cship')->select("SELECT StatusDetails, AwbNo, CreatedDate
        FROM (
              SELECT StatusDetails, AwbNo, CreatedDate
                    , ROW_NUMBER() OVER(PARTITION BY StatusDetails ORDER BY CreatedDate desc)row_num
              FROM PODTrans Where FPCode = 'BOMBINO' 
            ) sub
        WHERE row_num = 1");
    $bombino_each_staus_detials = [];
    $ignore = 'Run No.';
    $offset = 0;
    foreach ($kyc_status as $value) {

      if (!str_contains($value->StatusDetails, $ignore)) {

        $date = $value->CreatedDate;
        $final_date = $this->CarbonGetDateDiff($date);
        $bombino_each_staus_detials[$offset]=
        [ 
          'Status' => $value->StatusDetails,
          'day' => $final_date['Days'],
          'time' => $final_date['time'],
        ];

        $offset++;
      }
    }
    return $bombino_each_staus_detials;
  }


  public function BlueDartAndDeliveryStatus()
  {
    $delivery_last_update = DB::connection('b2cship')->select("SELECT PacketStatus, AwbNo, CreatedDate, FPCode
    FROM (
          SELECT PacketStatus, AwbNo, CreatedDate, FPCode
                , ROW_NUMBER() OVER(PARTITION BY FPCode ORDER BY CreatedDate desc)row_num
          FROM PODTrans Where FPCode IN ('BLUEDART', 'DL Delhi', 'DELIVERY') AND PacketStatus = 'DELIVERED'
        ) sub
    WHERE row_num = 1");

    $delivery_status = [];
    foreach ($delivery_last_update as $key => $value) {
      $date = $value->CreatedDate;
      $final_date = $this->CarbonGetDateDiff($date);
      $delivery_status[$key] = [

        'StatusDetails' => $value->PacketStatus . ' [ ' . $value->AwbNo . ' ] ',
        'FPCode' => $value->FPCode,
        'day' => $final_date['Days'],
        'time' => $final_date['time'],
      ];
    }
    return $delivery_status;
  }
}
