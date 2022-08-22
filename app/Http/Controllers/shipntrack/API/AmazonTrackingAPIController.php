<?php

namespace App\Http\Controllers\shipntrack\API;

use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\EventMapping\TrackingEventMapping;

class AmazonTrackingAPIController extends Controller
{
    function B2cShipTrackingResponse(Request $request)
    {
        $requestContent = $request->getContent();
        $results = '';

        try {

            $xmlObject = simplexml_load_string($requestContent);
            $json = json_encode($xmlObject);
            $phpArray = json_decode($json, true);

            $user_id = $phpArray['Validation']['UserID'];
            $password = $phpArray['Validation']['Password'];

            $final_data = getTrackingDetails($phpArray['TrackingNumber']);

            if (!empty(($final_data))) {

                $results = '<APIVersion>1</APIVersion>
                        <PackageTrackingInfo>
                            <PackageDeliveryDate>
                                <ReScheduleDeliveryDate/>
                                <ScheduleDeliveryDate/> 
                            </PackageDeliveryDate>
                            <PackageDestinationLocation>
                                <City></City>
                                <CountryCode>' . json_decode($final_data['shipping_address'])->CountryCode . '</CountryCode>
                            </PackageDestinationLocation>';

                foreach ($final_data['tracking_details'] as $value) {

                    $record = TrackingEventMapping::where('our_event_description', $value['Activity'])->orderby('updated_at', 'desc')->first();
                    $results  .= '<TrackingEventHistory>
                            <TrackingEventDetail>
                                <EventDateTime>' . date("Y-m-d\TH:i:s\Z", strtotime($value['Date_Time'])) . '</EventDateTime>
                                <EventLocation>
                                    <City></City>
                                    <CountryCode></CountryCode>
                                    <PostalCode></PostalCode>
                                    <StateProvince></StateProvince>
                                </EventLocation>
                                <EventReason>' . $record->our_event_description . '</EventReason>
                                <EventStatus>' . $record->our_event_code . '</EventStatus>
                                <SignedForByName/>
                            </TrackingEventDetail>
                        </TrackingEventHistory>';
                }
                $results .= '</PackageTrackingInfo>';

                return $results;
            }
        } catch (Exception $e) {

            echo 'Invalid Request';
            return false;
        }
    }
}
