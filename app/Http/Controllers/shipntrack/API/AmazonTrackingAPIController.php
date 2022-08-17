<?php

namespace App\Http\Controllers\shipntrack\API;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\EventMapping\TrackingEventMapping;

class AmazonTrackingAPIController extends Controller
{
    function B2cShipTrackingResponse(Request $request)
    {
        $final_data = getTrackingDetails($request->awbNo);
        // return $shipping = json_decode($final_data['shipping_address'])->CountryCode;
        $results = [];
        $results = '<APIVersion>1</APIVersion>
                    <PackageTrackingInfo>
                        <PackageDeliveryDate>
                            <ReScheduleDeliveryDate/>
                            <ScheduleDeliveryDate/> 
                        </PackageDeliveryDate>
                        <PackageDestinationLocation>
                            <City></City>
                            <CountryCode>'.json_decode($final_data['shipping_address'])->CountryCode.'</CountryCode>
                        </PackageDestinationLocation>';

            foreach($final_data['tracking_details'] as $value)
            {
                $records = TrackingEventMapping::where('our_event_description', $value['Activity'])->get();
                foreach($records as $record)
                {
                    // $result [] = [
                    //     'count' => $count,
                    //     'Event_description' => $record->our_event_description,
                    //     'Event_code' => $record->our_event_code,
                    //     'Time_zone' => date("Y-m-d\TH:i:s\Z", strtotime($value['Date_Time'])),
                    //     'Location' => $value['Location'],
                    // ];
                    

                    $results  .= '<TrackingEventHistory>
                        <TrackingEventDetail>
                            <EventDateTime>'.date("Y-m-d\TH:i:s\Z", strtotime($value['Date_Time'])).'</EventDateTime>
                            <EventLocation>
                                <City>'."Bangaluru".'</City>
                                <CountryCode>'."INDIA".'</CountryCode>
                                <PostalCode></PostalCode>
                                <StateProvince>'."BLG".'</StateProvince>
                            </EventLocation>
                            <EventReason>'.$record->our_event_description.'</EventReason>
                            <EventStatus>'.$record->our_event_code.'</EventStatus>
                            <SignedForByName/>
                        </TrackingEventDetail>
                    </TrackingEventHistory>';
                }
            }
            $results .= '</PackageTrackingInfo>';
           return $results;
        
    }
}
