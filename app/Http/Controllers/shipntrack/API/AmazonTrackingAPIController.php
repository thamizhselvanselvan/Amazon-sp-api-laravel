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

            if ($user_id != 'Amazon' || $password != 'AcZmraDzLoxA4NxLUcyrWnSiEaXxRQkfJ9B5hCbiK5M=') {

                echo '<?xml version="1.0" encoding="UTF-8"?>
                <AmazonTrackingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
                <ValidationError>
                    <UserIDError>Invalid</UserIDError>
                    <PasswordError>Invalid</PasswordError>
                </ValidationError>
                </AmazonTrackingRequest>';
                return false;
            }

            $final_data = getTrackingDetails($phpArray['TrackingNumber']);

            if ($final_data != 'Invalid AWB') {

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

                    if (isset($record->our_event_description) && isset($record->our_event_code)) {

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
                }

                $results .= '</PackageTrackingInfo>';
                return $results;
            } else {

                echo '<?xml version="1.0" encoding="UTF-8"?>
                <AmazonTrackingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
                <APIVersion>1.0</APIVersion>
                <TrackingNumberError>Invalid AWB: US30000006</TrackingNumberError>
                </AmazonTrackingRequest>';
                return false;
            }
        } catch (Exception $e) {

            echo '<?xml version="1.0" encoding="UTF-8"?>
            <AmazonTrackingRequestError xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
            Invalid Request
            </AmazonTrackingRequestError>';
            return false;
        }
    }
}
