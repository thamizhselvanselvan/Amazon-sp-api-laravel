<?php

namespace App\Services\ShipNTrack\Tracking;

use App\Models\ShipNTrack\Aramex\AramexTracking;
use Carbon\Carbon;
use Exception;
use Hamcrest\Type\IsString;

class AramexTrackingServices
{
    private $fillable = [
        'WaybillNumber' => 'awbno',
        'UpdateCode' => 'update_code',
        'UpdateDescription' => 'update_description',
        'UpdateDateTime' => 'update_date_time',
        'UpdateLocation' => 'update_location',
        'Comments' => 'comment',
        'GrossWeight' => 'gross_weight',
        'ChargeableWeight' => 'chargeable_weight',
        'WeightUnit' => 'weight_unit',
        'ProblemCode' => 'problem_code'
    ];
    public function TrackingDetails($tracking_awb)
    {
        $tracking_array = $this->TrackingAPI($tracking_awb);
        $tracking_details = $this->TrackingDataFormating($tracking_array);

        // dd(($tracking_details['Tracking_details']));
        AramexTracking::upsert(
            $tracking_details['Tracking_details'],
            'awbno_update_date_time_unique',
            [
                'awbno',
                'update_code',
                'update_description',
                'update_date_time',
                'update_location',
                'comment',
                'gross_weight',
                'chargeable_weight',
                'weight_unit',
                'problem_code'
            ]
        );
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
        $tracking_data = [];

        if (array_key_exists(0, $tracking_details)) {

            foreach ($tracking_details as $data) {

                foreach ($data['a_Value']['TrackingResult'] as $details) {

                    $tem_array = [
                        'awbno' => 'NA',
                        'update_code' => 'NA',
                        'update_description' => 'NA',
                        'update_date_time' => 'NA',
                        'update_location' => 'NA',
                        'comment' => 'NA',
                        'gross_weight' => 'NA',
                        'chargeable_weight' => 'NA',
                        'weight_unit' => 'NA',
                        'problem_code' => 'NA'
                    ];
                    foreach ($details as $key => $value) {

                        if (is_string($value)) {

                            if (array_key_exists($key, $this->fillable)) {

                                if ($this->fillable[$key] == 'update_date_time') {

                                    $date_formate = Carbon::parse($value)->format('Y-m-d H:i:s');
                                    $tem_array[$this->fillable[$key]] = $date_formate;
                                } else {
                                    $tem_array[$this->fillable[$key]] = $value;
                                }
                            }
                        }
                    }
                    $tracking_array_tem[] = $tem_array;
                }
                $tracking_array[] = $tracking_array_tem;
                $tracking_array_tem = [];
            }
            return $tracking_array;
        } else {

            foreach ($tracking_details['a_Value']['TrackingResult'] as $details) {
                $tem_array = [
                    'awbno' => 'NA',
                    'update_code' => 'NA',
                    'update_description' => 'NA',
                    'update_date_time' => 'NA',
                    'update_location' => 'NA',
                    'comment' => 'NA',
                    'gross_weight' => 'NA',
                    'chargeable_weight' => 'NA',
                    'weight_unit' => 'NA',
                    'problem_code' => 'NA'
                ];
                foreach ($details as $key => $value) {

                    if (is_string($value)) {

                        if (array_key_exists($key, $this->fillable)) {

                            if ($this->fillable[$key] == 'update_date_time') {

                                $date_formate = Carbon::parse($value)->format('Y-m-d H:i:s');
                                $tem_array[$this->fillable[$key]] = $date_formate;
                            } else {

                                $tem_array[$this->fillable[$key]] = $value;
                            }
                        }
                    }
                }
                $tracking_array[] = $tem_array;
            }
            return $tracking_array;
        }
    }
}
