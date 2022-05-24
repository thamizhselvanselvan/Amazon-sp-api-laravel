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
    $kyc_booking_status = $this->BookingAndKycStatusDetails();

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
    return view('b2cship.dashboard', compact(['status_detials_array', 'bombino_status', 'delivery_status','kyc_booking_status']));
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
      'Days' => $differnce->days > 1 ? $differnce->days . ' Days' : 'Today',
      'time' => rtrim($final_date, ' ,') . ' Before'
    ];
  }
  public function BookingAndKycStatusDetails()
  {
    $kyc_received = DB::connection('b2cship')->select("SELECT TOP 1 AWBNO, CreatedDate
    FROM Packet WHERE IsKYC ='true' ORDER BY CreatedDate DESC");

    $kyc_status = DB::connection('b2cship')->select("SELECT AwbNo, CreatedDate, Status
        FROM (
              SELECT Status, AwbNo, CreatedDate
                    , ROW_NUMBER() OVER(PARTITION BY ISRejected ORDER BY CreatedDate desc)row_num
              FROM KYCStatus Where Status != '' 
            ) sub
        WHERE row_num = 1");

    $b2c_booking = DB::connection('b2cship')->select("SELECT TOP 1 AWBNO, CreatedDate
    FROM Packet ORDER BY CreatedDate DESC");
    
    $date_booking = $this->CarbonGetDateDiff($b2c_booking[0]->CreatedDate);

    $b2c_booking_array[0] = [
      'Status' => 'B2CShip Booking',
      'AwbNo' => $b2c_booking[0]->AWBNO,
      'day' => $date_booking['Days'],
      'time' => $date_booking['time']
    ];

    $date_time = $this->CarbonGetDateDiff($kyc_received[0]->CreatedDate);
    $kyc_received_array[0] = [
      'Status' => 'KYC Received',
      'AwbNo' => $kyc_received[0]->AWBNO,
      'day' => $date_time['Days'],
      'time' => $date_time['time']
    ];

    $kyc_status_array = [];
    foreach($kyc_status as $key => $value)
    {
      $date = $value->CreatedDate;
      $date_time = $this->CarbonGetDateDiff($date);

      $kyc_status_array[$key] = [

        'Status' => 'KYC '.$value->Status,
        'AwbNo' => $value->AwbNo,
        'day' => $date_time['Days'],
        'time' => $date_time['time']
      ];
    }

    return array_merge($b2c_booking_array, $kyc_status_array, $kyc_received_array);
  }

  public function BombinoStatus()
  {
    $bombino_status = DB::connection('b2cship')->select("SELECT StatusDetails, AwbNo, CreatedDate
        FROM (
              SELECT StatusDetails, AwbNo, CreatedDate
                    , ROW_NUMBER() OVER(PARTITION BY StatusDetails ORDER BY CreatedDate desc)row_num
              FROM PODTrans Where FPCode = 'BOMBINO' 
            ) sub
        WHERE row_num = 1 ORDER BY CreatedDate DESC");
    $bombino_each_staus_detials = [];
    $ignore = 'Run No.';
    $offset = 0;
    foreach ($bombino_status as $value) {

      if (!str_contains($value->StatusDetails, $ignore)) {

        $date = $value->CreatedDate;
        $final_date = $this->CarbonGetDateDiff($date);
        $bombino_each_staus_detials[$offset] =
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
          FROM PODTrans Where FPCode IN ('BLUEDART', 'DL Delhi', 'DELHIVERY') AND PacketStatus = 'DELIVERED'
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
