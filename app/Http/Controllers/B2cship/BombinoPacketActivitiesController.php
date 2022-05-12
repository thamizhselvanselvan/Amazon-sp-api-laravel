<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;

class BombinoPacketActivitiesController extends Controller
{
    public function PacketActivitiesDetails()
    {
        $packet_detials = DB::connection('mssql')->select("SELECT 
          DISTINCT TOP 10000 AwbNo,
          packetstatus = STUFF((
               SELECT distinct  ',' + POD1.StatusDetails
               FROM PODTrans POD1
               WHERE POD.AwbNo = POD1.AwbNo AND FPCode = 'BOMBINO'
               FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, ''),
          packetlocation = STUFF((
               SELECT  ',' + POD2.PODLocation
               FROM PODTrans POD2
               WHERE POD.AwbNo = POD2.AwbNo AND FPCode = 'BOMBINO'
               FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
          from PODTrans POD
          WHERE FPCode ='BOMBINO' 
          Group By AwbNo, PODLocation
          ORDER BY AwbNo DESC
     ");

     $pd_final_array = [];
     $offset = 0;
     foreach ($packet_detials as $value) {

          $packet_status = $value->packetstatus;
          $packet_location = $value->packetlocation;
          $packet_array = explode(',', $packet_status);
          $pl_array = explode(',', $packet_location);

          foreach ($packet_array as $key => $status) {
               $pd_final_array[$offset][0] = $value->AwbNo;
               $pd_final_array[$offset][$key + 1] = $status . ' [' . $pl_array[$key] . ']';
          }
          $offset++;
     }

    //  po($pd_final_array);
        return view('b2cship.bombinoActivities.index', compact(['pd_final_array']));
    }
}
