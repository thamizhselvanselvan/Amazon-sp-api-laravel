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

        $packet_status =
            [
                "Awb" => NULL,
                "SHIPMENT INFORMATION RECEIVED" => NULL,
                "SHIPMENT READY FOR DEPARTURE" => NULL,
                "IN TRANSIT" => NULL,
                "FLIGHT DELAY" => NUll,
                "SHIPMENT FORWARDED FROM NEW YORK TO DELHI" => NULL,
                "ARRIVED AT AIRPORT" => NULL,
                "CLEARANCE IN PROGRESS" => NULL,
                "SHIPMENT RECEIVED AT DELHI FROM NEW YORK" => NULL,
                "OFD" => NULL,
                "OUT FOR DELIVERY" => NULL,
                "DELIVERED" => NULL,
            ];

        $file_path = 'Bombino';
        $final_array = [];
        $pd_final_array = [];

        if (!file_exists(storage_path('app/' . $file_path))) {

            $pd_final_array[] = [

                '0' => NULL,
            ];
            return view('b2cship.bombinoActivities.index', compact(['pd_final_array']));
        }

        $path = storage_path('app/' . $file_path);
        $files = (scandir($path));
        $new_files_list = [];
        $ignored = array('.', '..');
        foreach ($files as $key => $file) {
            if (!in_array($file, $ignored)) {
                $new_files_list[$file] =  date("y-m-d H:i:s", filemtime($path . '/' . $file));
            }
        }

        arsort($new_files_list);
        foreach ($new_files_list as $key => $files) {
            $content = Storage::get($file_path . '/' . $key);
            $content = json_decode($content);
            $final_array = array_merge($final_array, (array)$content);
        }
        $packet_detials = DB::connection('mssql')->select("SELECT DISTINCT
        AwbNo, PODLocation, StatusDetails,FPCode,CreatedDate 
        from PODTrans
        WHERE CreatedDate BETWEEN convert(datetime, '$today_sd') AND convert(datetime,'$today_ed')
        AND FPCode ='BOMBINO' 
        ORDER BY CreatedDate DESC");

        $final_array = array_merge($final_array, $packet_detials);

        $pd_collect = collect($final_array);
        $pd_details = $pd_collect->groupBy('AwbNo');

        $offset = 0;
        $temp_packetStauts = [];
        foreach ($pd_details as $pd_key => $pd_value) {

            $packet_status =
            [
                "Awb" => NULL,
                "SHIPMENT INFORMATION RECEIVED" => NULL,
                "SHIPMENT READY FOR DEPARTURE" => NULL,
                "IN TRANSIT" => NULL,
                "FLIGHT DELAY" => NUll,
                "SHIPMENT FORWARDED FROM NEW YORK TO DELHI" => NULL,
                "ARRIVED AT AIRPORT" => NULL,
                "CLEARANCE IN PROGRESS" => NULL,
                "CLEARANCE PROCEDURE IN PROGRESS" => NULL,
                "SHIPMENT RECEIVED AT DELHI FROM NEW YORK" => NULL,
                "OFD" => NULL,
                "OUT FOR DELIVERY" => NULL,
                "DELIVERED" => NULL,
            ];

            $suboffset = 0;
            $packet_status['Awb'] = $pd_key;
            foreach ($pd_value as $pd_data) {

                $statusDetails = strtoupper(trim($pd_data->StatusDetails));
                if (array_key_exists($statusDetails, $packet_status)) {

                    $packet_status[$statusDetails]  = 1;
                }
            }
            $pd_final_array[$offset]= $packet_status;
            $offset++;
        }



        // exit;

        // foreach ($pd_details as $pd_key => $pd_value) {

        //     $suboffset = 0;
        //     $pd_final_array[$offset][$suboffset] = $pd_key;
        //     foreach ($pd_value as $pd_data) {

        //         $suboffset++;
        //         $pod_location = $pd_data->PODLocation;
        //         $created_date = substr($pd_data->CreatedDate, 0, 10);
        //         $statusDetails = trim($pd_data->StatusDetails);

        //         $pd_final_array[$offset][$suboffset] = $statusDetails ;
        //     }
        //     $offset++;
        // }

        return view('b2cship.bombinoActivities.index', compact(['pd_final_array']));
    }

    public function UpdatePacketDetails()
    {
        $today_sd = Carbon::today();
        $today_ed = Carbon::now();
        $year = $today_ed->year;

        // echo $year;
        // exit;
        $current_month = $today_ed->format('m');
        // $month = 2;
        for ($month = 1; $month <= $current_month; $month++) {

            if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

                $base_path = base_path();
                $command = "cd $base_path && php artisan pms:bombino-packet-activities $month $year> /dev/null &";
                exec($command);
            } else {

                Artisan::call('pms:bombino-packet-activities ' . $month . ' ' . $year);
            }
        }

        return redirect()->back();
    }
}
