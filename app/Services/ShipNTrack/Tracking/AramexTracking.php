<?php

namespace App\Services\ShipNTrack\Tracking;

use Exception;
use Hamcrest\Type\IsString;

class AramexTracking
{
    public function TrackingDetails($tracking_awb)
    {
        $tracking_array = $this->TrackingAPI($tracking_awb);
        $tracking_details = $this->TrackingDataFormating($tracking_array);
        return $tracking_details;
    }

    public function TrackingAPI($tracking_awb)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc/json/TrackShipments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "ClientInfo": {
                "UserName": "test.api@aramex.com",
                "Password": "Aramex@12345",
                "Version": "v1.0",
                "AccountNumber": "60531487",
                "AccountPin": "654654",
                "AccountEntity": "BOM",
                "AccountCountryCode": "IN",
                "Source": 10
            },
            "GetLastTrackingUpdateOnly": false,
            "Shipments": ["' . $tracking_awb . '"],
            "Transaction": {
                "Reference1": "",
                "Reference2": "",
                "Reference3": "",
                "Reference4": "",
                "Reference5": ""
            }
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = mungXML($response);
        $arrayResult = json_decode(json_encode(SimpleXML_Load_String($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        // dd($arrayResult);
        return $arrayResult;
    }

    public function TrackingDataFormating($arrayResult)
    {
        $tracking_details_array = [];
        $invalid_awb_detials = [];

        if (isset($arrayResult['TrackingResults'])) {

            $error = $arrayResult['HasErrors'];
            if ($error != 'true') {
                if (isset($arrayResult['TrackingResults']['a_KeyValueOfstringArrayOfTrackingResultmFAkxlpY'])) {

                    $tracking_details = $arrayResult['TrackingResults']['a_KeyValueOfstringArrayOfTrackingResultmFAkxlpY'];
                    $tracking_details_array = $this->TrackingDetailsFormating($tracking_details);
                }

                if (isset($arrayResult['NonExistingWaybills']['a_string'])) {

                    $invalid_awb = $arrayResult['NonExistingWaybills']['a_string'];
                    $invalid_awb_detials = $this->InvalidAwb($invalid_awb);
                }
            } else {
                //
            }

            return [
                'Tracking_details' => $tracking_details_array,
                'Invalid_Awb' => $invalid_awb_detials
            ];
        }
    }

    public function InvalidAwb($invalid_awb)
    {
        // if (is_array($invalid_awb)) {

        //     dd($invalid_awb);
        // } else {

        //     dd($invalid_awb);
        // }
        return true;
    }

    public function TrackingDetailsFormating($tracking_details)
    {
        $tracking_array = [];
        $tracking_array_tem = [];

        if (array_key_exists(0, $tracking_details)) {
            foreach ($tracking_details as $data) {
                foreach ($data['a_Value']['TrackingResult'] as $details) {
                    foreach ($details as $key => $value) {
                        $tracking_data[$key] = $value;
                    }
                    $tracking_array_tem[] = $tracking_data;
                }
                $tracking_array[] = $tracking_array_tem;
                $tracking_array_tem = [];
            }
            return $tracking_array;
        } else {
            foreach ($tracking_details['a_Value']['TrackingResult'] as $details) {

                foreach ($details as $key => $value) {

                    if (is_string($value)) {
                        $tracking_data[$key] = $value;
                    }
                }
                $tracking_array[] = $tracking_data;
            }
            return $tracking_array;
        }
    }
}
