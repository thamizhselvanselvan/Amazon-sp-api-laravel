<?php

namespace App\Services\ShipNTrack\API;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\ShipNTrack\EventMapping\TrackingEventMapping;

class AmazonTrackingRequest
{
    public function TrackingMaster($requestContent)
    {
        try {
            $xmlObject = simplexml_load_string($requestContent);
            $json = json_encode($xmlObject);
            $phpArray = json_decode($json, true);

            $user_id = $phpArray['Validation']['UserID'];
            $password = $phpArray['Validation']['Password'];

            $tracking_awb = $phpArray['TrackingNumber'];

            $valid_user_id = 'Amazon';
            $valid_password = 'AcZmraDzLoxA4NxLUcyrWnSiEaXxRQkfJ9B5hCbiK5M=';

            if ($user_id != $valid_user_id || $password != $valid_password) {

                echo '<AmazonTrackingResponse xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <APIVersion>1</APIVersion>
                <TrackingErrorInfo>
                    <TrackingNumber>' . $tracking_awb . '</TrackingNumber>
                    <TrackingErrorDetail>
                        <ErrorDetailCode>ERROR_201</ErrorDetailCode>
                        <ErrorDetailCodeDesc>INVALID USERID/PASSWORD</ErrorDetailCodeDesc>
                    </TrackingErrorDetail>
                </TrackingErrorInfo>
            </AmazonTrackingResponse>';

                return false;
            }

            if (str_contains($tracking_awb, 'US1') && strlen($tracking_awb) == 10) {

                return $this->B2CShipAmazonTrackingResponse($user_id, $password, $tracking_awb);
            } elseif (str_contains($tracking_awb, 'US3') && strlen($tracking_awb) == 10) {

                return $this->SNTAmazonTrackingResponse($tracking_awb);
            } else {

                return $this->InvalidAwbNo($tracking_awb);
            }
        } catch (Exception $e) {

            echo '<AmazonTrackingResponse xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <APIVersion>1.0</APIVersion>
            <TrackingErrorInfo>
                <TrackingNumber></TrackingNumber>
                <TrackingErrorDetail>
                    <ErrorDetailCode>ERROR_301</ErrorDetailCode>
                    <ErrorDetailCodeDesc>TRACKING SERVICE NOT AVAILABLE</ErrorDetailCodeDesc>
                </TrackingErrorDetail>
            </TrackingErrorInfo>
        </AmazonTrackingResponse>';
            return false;
        }
    }

    public function SNTAmazonTrackingResponse($tracking_awb)
    {
        $final_data = getTrackingDetails($tracking_awb);

        if ($final_data != 'Invalid AWB') {

            $results = '<AmazonTrackingResponse xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <APIVersion>1</APIVersion>
                        <PackageTrackingInfo>
                         <TrackingNumber>' . $tracking_awb . '</TrackingNumber>
                            <PackageDestinationLocation>
                                <City></City>
                                <CountryCode>' . json_decode($final_data['shipping_address'])->CountryCode . '</CountryCode>
                            </PackageDestinationLocation>
                            <PackageDeliveryDate>
                                <ScheduledDeliveryDate></ScheduledDeliveryDate>
                                <ReScheduledDeliveryDate></ReScheduledDeliveryDate>
                            </PackageDeliveryDate>
                            <TrackingEventHistory>';

            foreach ($final_data['tracking_details'] as $value) {

                $record = TrackingEventMapping::where('our_event_description', $value['Activity'])->orderby('updated_at', 'desc')->first();

                if (isset($record->our_event_description) && isset($record->our_event_code)) {

                    $results  .=
                        '<TrackingEventDetail>
                            <EventStatus>' . $record->our_event_code . '</EventStatus>
                            <EventReason>' . $record->our_event_description . '</EventReason>
                            <EventDateTime>' . date("Y-m-d\TH:i:s\Z", strtotime($value['Date_Time'])) . '</EventDateTime>
                                <EventLocation>
                                    <City></City>
                                    <StateProvince></StateProvince>
                                    <PostalCode></PostalCode>
                                    <CountryCode></CountryCode>
                                </EventLocation>
                            <SignedForByName></SignedForByName>
                        </TrackingEventDetail>';
                }
            }
            $results .= '
                </TrackingEventHistory>
        </PackageTrackingInfo>
    </AmazonTrackingResponse>';
            return $results;
        } else {

            return $this->InvalidAwbNo($tracking_awb);
        }
    }


    public function B2CShipAmazonTrackingResponse($user_id, $password, $tracking_awb)
    {
        $curl = curl_init();
        $url = 'https://uat-api.b2cship.us/PacificAmazonAPI.svc/TrackingAmazon';

        $header  = array('Content-Type: text/plain');

        $post_field = "<?xml version='1.0' encoding='UTF-8'?>
        <AmazonTrackingRequest xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
        xsi:noNamespaceSchemaLocation='AmazonTrackingRequest.xsd'>
        <Validation>
        <UserID>$user_id</UserID>
        <Password>$password</Password>
        </Validation>
        <APIVersion>1.0</APIVersion>
        <TrackingNumber>$tracking_awb</TrackingNumber>
        </AmazonTrackingRequest>
        ";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post_field,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function InvalidAwbNo($tracking_awb)
    {

        echo '<AmazonTrackingResponse xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <APIVersion>1</APIVersion>
        <TrackingErrorInfo>
            <TrackingNumber>' . $tracking_awb . '</TrackingNumber>
            <TrackingErrorDetail>
                <ErrorDetailCode>ERROR_101</ErrorDetailCode>
                <ErrorDetailCodeDesc>INVALID TRACKING NUMBER</ErrorDetailCodeDesc>
            </TrackingErrorDetail>
        </TrackingErrorInfo>
    </AmazonTrackingResponse>';
        return false;
    }
}
