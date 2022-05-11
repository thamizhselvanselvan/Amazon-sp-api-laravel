<?php

namespace App\Http\Controllers\B2cship;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BombinoPacketActivitiesController extends Controller
{
    public function PacketActivitiesDetails()
    {
        $packet_detials = DB::connection('mssql')->select("SELECT TOP 10000 AwbNo, PODLocation, StatusDetails, FPCode, CreatedDate from PODTrans  WHERE FPCode ='BOMBINO' ORDER BY AwbNo DESC, CreatedDate DESC");
        $packet_detials = collect($packet_detials);
        $packet_detials = $packet_detials->groupBy('AwbNo');

        $pd_final_array = [];
        $offset = 0;
        foreach ($packet_detials as $pd_key => $pd_value) {

            $suboffset = 0;
            $pd_final_array[$offset][$suboffset] = $pd_key;
            foreach ($pd_value as $pd_data) {

                $suboffset++;
                $pod_location = $pd_data->PODLocation;
                $created_date = substr($pd_data->CreatedDate, 0, 10);
                $statusDetails = trim($pd_data->StatusDetails);

                $pd_final_array[$offset][$suboffset] = $statusDetails;
            }
            $offset++;
        }

        // po(($pd_final_array));

        // exit;
        return view('b2cship.bombinoActivities.index', compact(['pd_final_array']));
    }
}
