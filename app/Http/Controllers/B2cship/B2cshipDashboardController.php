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
    $bombino_last_update = DB::connection('b2cship')->select("SELECT TOP 1 CreatedDate  FROM PODTrans WHERE FPCode ='BOMBINO' ORDER BY CreatedDate DESC");
    $bluedart_last_update = DB::connection('b2cship')->select("SELECT TOP 1 CreatedDate  FROM PODTrans WHERE FPCode ='BLUEDART' ORDER BY CreatedDate DESC");
    $dl_delhi_last_update = DB::connection('b2cship')->select("SELECT TOP 1 CreatedDate  FROM PODTrans WHERE FPCode ='DL DELHI' ORDER BY CreatedDate DESC");
    $delivery_last_update = DB::connection('b2cship')->select("SELECT TOP 1 CreatedDate  FROM PODTrans WHERE FPCode ='DELIVERY' ORDER BY CreatedDate DESC");

    $date_details_array =['Year','Month','Day','Hour', 'Minute', 'Second'];
    
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
          $final_date .= $value > 1? $value .' ' .$date_details_array[$count].'s,  ' : $value .' ' .$date_details_array[$count].',  ';
          // $final_date .= $value .' ' .$date_details_array[$count].',  ';
        }
        $count ++;
      }
      return rtrim($final_date, ',  ').' before';
    }
    else{
      return 'Details Not Avaliable';
    }
  }
}
