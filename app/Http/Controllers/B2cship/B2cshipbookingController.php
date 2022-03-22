<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class B2cshipbookingController extends Controller
{
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
        $last30DaysTotalBooking = $this->bookingDetails($startTime,$endTime);




      return view('b2cship.booking.index', compact('todayTotalBooking','yesterdayTotalBooking','Last7DaysTotalBooking','last30DaysTotalBooking'));
        
    }
      public function bookingDetails($start, $end)
        {
            $intransit = 0;
            $booked = 0;
            $Ofd =0;
            $delivered=0;
            $undeliverd=0;
         $totalBookings = DB::connection('mssql')->select("SELECT PacketStatus FROM Packet WHERE  CreatedDate BETWEEN '$start' AND '$end'");
    //    po($totalBookings);
    //    exit;
           foreach($totalBookings as $totalBooking)
           {
               if($totalBooking->PacketStatus == 'INTRANSIT'){ 
                 $intransit ++;
               }
               elseif($totalBooking->PacketStatus == 'BOOKED')
               {
                $booked ++;
               }
               elseif($totalBooking->PacketStatus == 'Undelivered')
               {
                $undeliverd ++;
               }
               elseif($totalBooking->PacketStatus == 'Ofd')
               {
                   $Ofd ++;
               }
               elseif($totalBooking->PacketStatus == 'Delivered')
                {
                    $delivered ++;
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
 