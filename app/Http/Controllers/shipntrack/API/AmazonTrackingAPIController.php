<?php

namespace App\Http\Controllers\shipntrack\API;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\EventMapping\TrackingEventMapping;

class AmazonTrackingAPIController extends Controller
{
    function B2cShipTrackingResponse(Request $request)
    {
        $apidata = bombino_tracking($request->awbNo);

        $apidata2 = smsa_tracking($request->awbNo);
        $final_data = array_merge($apidata, $apidata2);
        // return $final_data;
        // exit;
        
        foreach($final_data as $key => $value)
        {
            $records = TrackingEventMapping::where('our_event_description', $value['Activity'])->get();
           foreach($records as $record)
           {
               $result [] = [
                'Event_description' => $record->our_event_description,
                'Event_code' => $record->our_event_code,
                'Time_zone' => date("Y-m-d\TH:i:s\Z", strtotime($value['Date_Time'])),
                'Location' => $value['Location'],
               ];
            }
        }
        return ($result);
    }
}
