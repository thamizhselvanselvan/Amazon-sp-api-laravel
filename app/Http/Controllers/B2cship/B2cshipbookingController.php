<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\Types\Null_;

class B2cshipbookingController extends Controller
{
  public function Dashboard()
  {
    
    $array = "'BOMBINO', 'BLUEDART', 'DELIVERY'";
    $bombino_last_update = DB::connection('mssql')->select("SELECT TOP 1 CreatedDate, AwbNo  FROM PODTrans WHERE FPCode ='BOMBINO' ORDER BY CreatedDate DESC");
    $bluedart_last_update = DB::connection('mssql')->select("SELECT TOP 1 CreatedDate, AwbNo  FROM PODTrans WHERE FPCode ='BLUEDART' ORDER BY CreatedDate DESC");
    $dl_delhi_last_update = DB::connection('mssql')->select("SELECT TOP 1 CreatedDate, AwbNo  FROM PODTrans WHERE FPCode ='DL DELHI' ORDER BY CreatedDate DESC");
    $delivery_last_update = DB::connection('mssql')->select("SELECT TOP 1 CreatedDate, AwbNo  FROM PODTrans WHERE FPCode ='DELIVERY' ORDER BY CreatedDate DESC");
    
    $date_details_array =[
       'Year','Month','Day','Hours', 'Minutes', 'Second'
    ];
    
    $bombino_date = $this->CarbonDateDiff($bombino_last_update, $date_details_array);
    $dl_delhi_date = $this->CarbonDateDiff($dl_delhi_last_update, $date_details_array);
    $bluedart_date = $this->CarbonDateDiff($bluedart_last_update, $date_details_array);
    $delivery_date = $this->CarbonDateDiff($delivery_last_update, $date_details_array);

    return view('b2cship.dashboard', compact(['bombino_date', 'dl_delhi_date', 'bluedart_date','delivery_date']));
  }

  public function CarbonDateDiff($last_update_date, $date_details_array)
  {
    if($last_update_date)
    {
      $date =$last_update_date[0]->CreatedDate;
      $date = substr($date, 0, strpos($date, "."));
      $created = new Carbon($date);
      $now = Carbon::now();  
      $differnce = $created->diff($now);
      // po($differnce);
      $final_date ='';
      $count = 0;
      foreach((array)$differnce as $key => $value)
      {
        if($value != 0 && $count < 6) 
        {
          $final_date .= $value .' ' .$date_details_array[$count].',  ';
        }
        $count ++;
      }
      return rtrim($final_date, ',  ').' before';
    }
    else{
      return 'Data Not Avaliable';
    }
  }

  public function Bookingstatus()
  {
    $startTime = Carbon::today();
    $endTime = Carbon::now();
    $todayTotalBooking =  $this->bookingDetails($startTime, $endTime);

    $startTime = Carbon::yesterday();
    $endTimeYesterday = $startTime->toDateString();
    $endTimeYesterday = $endTimeYesterday . ' 23:59:59';
    $yesterdayTotalBooking =  $this->bookingDetails($startTime, $endTimeYesterday);

    $startTime = Carbon::today()->subDays(7);
    $Last7DaysTotalBooking =  $this->bookingDetails($startTime, $endTime);

    $startTime = Carbon::today()->subDays(30);
    $last30DaysTotalBooking = $this->bookingDetails($startTime, $endTime);




    return view('b2cship.booking.index', compact('todayTotalBooking', 'yesterdayTotalBooking', 'Last7DaysTotalBooking', 'last30DaysTotalBooking'));
  }
  public function bookingDetails($start, $end)
  {
    $intransit = 0;
    $booked = 0;
    $Ofd = 0;
    $delivered = 0;
    $undeliverd = 0;
    $totalBookings = DB::connection('mssql')->select("SELECT PacketStatus FROM Packet WHERE  CreatedDate BETWEEN '$start' AND '$end'");
    //    po($totalBookings);
    //    exit;
    foreach ($totalBookings as $totalBooking) {
      if ($totalBooking->PacketStatus == 'INTRANSIT') {
        $intransit++;
      } elseif ($totalBooking->PacketStatus == 'BOOKED') {
        $booked++;
      } elseif ($totalBooking->PacketStatus == 'UN-DELIVERED') {
        $undeliverd++;
      } elseif ($totalBooking->PacketStatus == 'OFD') {
        $Ofd++;
      } elseif ($totalBooking->PacketStatus == 'DELIVERED') {
        $delivered++;
      }
    }
    $finalArray['totalBooking'] = count($totalBookings);
    $finalArray['booked'] = $booked;
    $finalArray['Ofd'] = $Ofd;
    $finalArray['delivered'] = $delivered;
    $finalArray['UnDelivered'] = $undeliverd;
    $finalArray['intransit'] = $intransit;
    return ($finalArray);
  }
}
