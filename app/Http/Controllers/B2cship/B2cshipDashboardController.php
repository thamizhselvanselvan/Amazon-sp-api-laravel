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

    $array = "'BOMBINO', 'BLUEDART', 'DELIVERY'";
    $bombino_last_update = DB::connection('b2cship')->select("SELECT TOP 1 CreatedDate, AwbNo, StatusDetails  FROM PODTrans WHERE FPCode ='BOMBINO' ORDER BY CreatedDate DESC");
    $bluedart_last_update = DB::connection('b2cship')->select("SELECT TOP 1 CreatedDate, AwbNo, StatusDetails  FROM PODTrans WHERE FPCode ='BLUEDART' ORDER BY CreatedDate DESC");
    $dl_delhi_last_update = DB::connection('b2cship')->select("SELECT TOP 1 CreatedDate, AwbNo, StatusDetails  FROM PODTrans WHERE FPCode ='DL DELHI' ORDER BY CreatedDate DESC");
    $delivery_last_update = DB::connection('b2cship')->select("SELECT TOP 1 CreatedDate, AwbNo, StatusDetails  FROM PODTrans WHERE FPCode ='DELIVERY' ORDER BY CreatedDate DESC");

    $date_details_array = ['Year', 'Month', 'Day', 'Hour', 'Minute', 'Second'];

    $bombino_date = $this->CarbonDateDiff($bombino_last_update, $date_details_array);
    $dl_delhi_date = $this->CarbonDateDiff($dl_delhi_last_update, $date_details_array);
    $bluedart_date = $this->CarbonDateDiff($bluedart_last_update, $date_details_array);
    $delivery_date = $this->CarbonDateDiff($delivery_last_update, $date_details_array);

    $bombino_status = $this->BombinoStatus();
    return view('b2cship.dashboard', compact(['bombino_date', 'dl_delhi_date', 'bluedart_date', 'delivery_date','bombino_status']));
  }

  public function CarbonDateDiff($last_update_date, $date_details_array)
  {
    if ($last_update_date) {

      $date = $last_update_date[0]->CreatedDate;
      $date = substr($date, 0, strpos($date, "."));
      $created = new Carbon($date);
      $now = Carbon::now();
      $differnce = $created->diff($now);
      $final_date = '';
      $count = 0;
      foreach ((array)$differnce as $key => $value) {
        if ($value != 0 && $count < 6) {
          $final_date .= $value > 1 ? $value . ' ' . $date_details_array[$count] . 's,  ' : $value . ' ' . $date_details_array[$count] . ',  ';
        }
        $count++;
      }
      $time_array = [
        'lastRecord' => $last_update_date[0]->AwbNo . '  ' . $last_update_date[0]->StatusDetails,
        'Diff' => rtrim($final_date, ',  ') . ' before',
      ];
      return $time_array;
    } else {
      $time_array = [
        'lastRecord' => 'NA',
        'Diff' => 'NA'
      ];
      return $time_array;
    }
  }

  public function BookingAndKycStatusDetails()
  {
    $kyc_status = DB::connection('b2cship')->select("SELECT Status, AwbNo,CreatedDate
        FROM (
              SELECT Status, AwbNo, CreatedDate
                    , ROW_NUMBER() OVER(PARTITION BY Status ORDER BY CreatedDate desc)row_num
              FROM KYCStatus
            ) sub
        WHERE row_num = 1");

    po($kyc_status);
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
    $date_details_array = ['Year', 'Month', 'Day', 'Hour', 'Minute', 'Second'];
    $ignore = 'Run No.';
    $offset = 0;
    foreach ($kyc_status as $value) {

      if(!str_contains($value->StatusDetails, $ignore)) {

        $bombino_each_staus_detials[$offset]['Status'] = $value->StatusDetails;

        $date = $value->CreatedDate;
        $date = substr($date, 0, strpos($date, "."));
        $created = new Carbon($date);
        $now = Carbon::now();
        $differnce = $created->diff($now);
        $final_date = '';
        $count = 0;
        foreach ((array)$differnce as $value) {
          if ($value != 0 && $count < 6) {
            $final_date .= $value > 1 ? $value . ' ' . $date_details_array[$count] . 's,  ' : $value . ' ' . $date_details_array[$count] . ',  ';
          }
          $count++;
        }
        $bombino_each_staus_detials[$offset]['updatedDate'] = $final_date;
        $offset++;
      }
    }
   return $bombino_each_staus_detials;
  }
}
