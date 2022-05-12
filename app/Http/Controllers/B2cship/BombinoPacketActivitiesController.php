<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;

class BombinoPacketActivitiesController extends Controller
{
    public function PacketActivitiesDetails()
    {
        $today_sd = Carbon::today();
        $today_ed = Carbon::now();

         


        exit;
        $id = 1;
        // echo $id;
        $start = ($id-1)*2000;
        $total_count = DB::connection('mssql')->select("SELECT DISTINCT COUNT(AwbNo) as awb from PODTrans WHERE FPCode = 'BOMBINO'");
        $total_count = $total_count[0]->awb;
        $total_count = (round($total_count / 2500));

        $packet_detials = DB::connection('mssql')->select("SELECT 
          DISTINCT AwbNo,
          packetstatus = STUFF((
               SELECT distinct  ',' + POD1.StatusDetails
               FROM PODTrans POD1
               WHERE POD.AwbNo = POD1.AwbNo AND FPCode = 'BOMBINO'
               FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
          from PODTrans POD
          WHERE FPCode ='BOMBINO' 
          Group By AwbNo, PODLocation
          ORDER BY AwbNo DESC
          OFFSET $start ROWS 
          FETCH NEXT 2000 ROWS ONLY
     ");

        $pd_final_array = [];
        $offset = 0;
        foreach ($packet_detials as $value) {

            $packet_status = $value->packetstatus;
            $packet_array = explode(',', $packet_status);

            foreach ($packet_array as $key => $status) {
                $pd_final_array[$offset][0] = $value->AwbNo;
                $pd_final_array[$offset][$key + 1] = $status;
            }
            $offset++;
        }

        //  po($pd_final_array);
        return view('b2cship.bombinoActivities.index', compact(['pd_final_array','total_count']));
    }

    public function UpdatePacketDetails()
    {
        $today_sd = Carbon::today();
        $today_ed = Carbon::now();
        
        $current_month = $today_sd->month;

        for($month = 1; $month<=$current_month; $month++)
        {
            if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            
                // exec('nohup php artisan pms:textiles-import  > /dev/null &');
               
                $base_path = base_path();
                $command = "cd $base_path && php artisan pms:bombino-packet-activities $month > /dev/null &";
                exec($command);
                
                // Log::warning("Export asin command executed production  !!!");
            } else {
    
                // Log::warning("Export asin command executed local !");
                Artisan::call('pms:bombino-packet-activities '.$month);
            }
        }
        // echo $current_mo
    }
}
