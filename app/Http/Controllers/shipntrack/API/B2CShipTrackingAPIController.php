<?php

namespace App\Http\Controllers\shipntrack\API;

use Exception;
use Illuminate\Http\Request;
use function Clue\StreamFilter\fun;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class B2CShipTrackingAPIController extends Controller
{
    public function B2CshipTrackingResponse(Request $request)
    {
        $requestContent = $request->getContent();
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

                $final_array = [];
                $tracking_address  = [];

                if (isset($final_data['tracking_details'])) {
                    foreach ($final_data['tracking_details'] as $value) {
                        $tracking_array[] = [
                            'Data_Time' => $value['Date_Time'],
                            'Location' => $value['Location'],
                            'Activity' => $value['Activity'],
                        ];
                    }
                }
                if (isset($final_data['shipping_address'])) {

                    $tracking_address[] =  $final_data['shipping_address'];
                }

                $final_array = [
                    'Tracking_Details' => $tracking_array,
                    'Shipping_Address' => $tracking_address
                ];

                return json_encode($final_array);
            } else {

                echo '<?xml version="1.0" encoding="UTF-8"?>
                <AmazonTrackingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
                <APIVersion>1.0</APIVersion>
                <TrackingNumberError>Invalid AWB: ' . $phpArray['TrackingNumber'] . '</TrackingNumberError>
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
