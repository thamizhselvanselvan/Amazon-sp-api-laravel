<?php

namespace App\Services\ShipNTrack\Tracking;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use App\Models\ShipNTrack\SMSA\AeSmsaTracking;
use App\Models\ShipNTrack\SMSA\KsaSmsaTracking;
use App\Models\ShipNTrack\Aramex\AeAramexTracking;
use App\Models\ShipNTrack\Bombino\BombinoTracking;
use App\Models\ShipNTrack\Aramex\KsaAramexTracking;

class CourierTracking
{
    public function AramexAPI($records)
    {
        $username = $records['user_name'];
        $passKey = $records['pass_key'];
        $awbNo = $records['awb_no'];
        $accoundId = $records['account_id'];
        $destination = strtolower($records['destination']);
        $process_management_id = $records['process_management'];

        $url = "https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc/json/TrackShipments";
        $payload =
            [
                "ClientInfo" => [
                    "UserName" => $username,
                    "Password" => $passKey,
                    "Version" => "v1.0",
                    "AccountNumber" => "60531487",
                    "AccountPin" => "654654",
                    "AccountEntity" => "BOM",
                    "AccountCountryCode" => "IN",
                    "Source" => 24
                ],
                "GetLastTrackingUpdateOnly" => false,
                "Shipments" => [
                    $awbNo
                ]
            ];

        $response = Http::withoutVerifying()->withHeaders([
            "Content-Type" => "application/json"
        ])->post($url, $payload);

        $this->AramexDataFormatting($response, $accoundId, $destination);

        $command_end_time = now();
        ProcessManagementUpdate($process_management_id, $command_end_time);
    }

    public function AramexDataFormatting($response, $accoundId, $destination)
    {
        $aramex_records = [];
        $aramex_data = isset(json_decode($response, true)['TrackingResults'][0]['Value']) ? json_decode($response, true)['TrackingResults'][0]['Value'] : [];

        foreach ($aramex_data as $key1 => $aramex_value) {

            if ($aramex_value['UpdateDescription'] == 'Delivered') {
            }
            foreach ($aramex_value as $key2 => $value) {

                $aramex_records[$key1]['account_id'] = $accoundId;
                $key2 = ($key2 == 'WaybillNumber')     ? 'awbno'              : $key2;
                $key2 = ($key2 == 'UpdateCode')        ? 'update_code'        : $key2;
                $key2 = ($key2 == 'UpdateDescription') ? 'update_description' : $key2;
                $key2 = ($key2 == 'UpdateDateTime')    ? 'update_date_time'   : $key2;
                $key2 = ($key2 == 'UpdateLocation')    ? 'update_location'    : $key2;
                $key2 = ($key2 == 'Comments')          ? 'comment'            : $key2;
                $key2 = ($key2 == 'ProblemCode')       ? 'problem_code'       : $key2;
                $key2 = ($key2 == 'GrossWeight')       ? 'gross_weight'       : $key2;
                $key2 = ($key2 == 'ChargeableWeight')  ? 'chargeable_weight'  : $key2;
                $key2 = ($key2 == 'WeightUnit')        ? 'weight_unit'        : $key2;

                if ($key2 == 'update_date_time') {

                    preg_match('/(\d{10})(\d{3})([\+\-]\d{4})/', $value, $matches);
                    $dt = DateTime::createFromFormat("U.u.O", vsprintf('%2$s.%3$s.%4$s', $matches));
                    $dt->setTimeZone(new DateTimeZone('Asia/Dubai'));
                    $date = $dt->format('Y-m-d H:i:s');

                    $aramex_records[$key1][$key2] = $date;
                } else {

                    $aramex_records[$key1][$key2] = $value;
                }
            }
        }
        // Log::notice($aramex_records);
        // Log::notice($destination);
        if ($destination == 'ae') {

            AeAramexTracking::upsert($aramex_records, ['awbno_update_timestamp_description_unique'], [
                'account_id',
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
            ]);
        } else if ($destination == 'ksa') {

            KsaAramexTracking::upsert($aramex_records, ['awbno_update_timestamp_description_unique'], [
                'account_id',
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
            ]);
        }
    }

    public function SmsaAPI($records)
    {
        // Log::debug($records);
        $passKey = $records['pass_key'];
        $awbNo = $records['awb_no'];
        $accoundId = $records['account_id'];
        $destination = strtolower($records['destination']);
        $process_management_id = $records['process_management'];

        $client = new Client();
        $headers = [
            'Content-Type' => 'text/xml'
        ];
        $body = '<?xml version=\'1.0\' encoding=\'utf-8\'?>
                <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <getTracking xmlns="http://track.smsaexpress.com/secom/">
                    <awbNo>' . $awbNo . '</awbNo>
                    <passkey>' . $passKey . '</passkey>
                    </getTracking>
                </soap:Body>
                </soap:Envelope>';
        $request = new Request('POST', 'http://track.smsaexpress.com/SeCom/SMSAwebService.asmx', $headers, $body);

        $response1 = $client->sendAsync($request)->wait();
        $plainXML = mungXML(trim($response1->getBody()));
        $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        // Log::debug($arrayResult);
        $smsa_data = $arrayResult['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram']['NewDataSet']['Tracking'];

        $smsa_records = [];
        if (isset($smsa_data[0])) {

            foreach ($smsa_data as $smsa_value) {
                $smsa_records[] = [
                    'account_id' => $accoundId,
                    'awbno' => $smsa_value['awbNo'],
                    'date' => date('Y-m-d H:i:s', strtotime($smsa_value['Date'])),
                    'activity' => $smsa_value['Activity'],
                    'details' => $smsa_value['Details'],
                    'location' => $smsa_value['Location']
                ];
            }
        } else {
            $smsa_records[] = [
                'account_id' => $accoundId,
                'awbno' =>  $smsa_data['awbNo'],
                'date' => date('Y-m-d H:i:s', strtotime($smsa_data['Date'])),
                'activity' =>  $smsa_data['Activity'],
                'details' =>  $smsa_data['Details'],
                'location' =>  $smsa_data['Location']
            ];
        }
        // Log::notice($smsa_records);
        // Log::notice($destination);
        if ($destination == 'ae') {

            SmsaTrackings::upsert($smsa_records, ['awbno_date_activity_unique'], [
                'account_id',
                'awbno',
                'date',
                'activity',
                'details',
                'location',
            ]);
        } elseif ($destination == 'ksa') {

            KsaSmsaTracking::upsert($smsa_records, ['awbno_date_activity_unique'], [
                'account_id',
                'awbno',
                'date',
                'activity',
                'details',
                'location',
            ]);
        }

        $command_end_time = now();
        ProcessManagementUpdate($process_management_id, $command_end_time);
    }

    public function BombinoAPI($records)
    {
        $account_id = $records['user_name'];
        $user_id = $records['pass_key'];
        $password = "123";
        $awbNo = $records['awb_no'];
        $url = "http://api.bombinoexp.in/bombinoapi.svc/Tracking?";

        $response = Http::withoutVerifying()->withHeaders([
            "Content-Type" => "application/json"
        ])->get($url . "AccountId=" . $account_id . "&UserId=" . $user_id . "&Password=" . $password . "&AwbNo=" . $awbNo);

        $bombino_records = json_decode($response, true);

        $records = $bombino_records['Shipments'][0]['TrackPoints'];
        $Bombino_Data = [

            "awb_no" => $bombino_records['Shipments'][0]['AWBNo'] ?? '',
            "consignee" => $bombino_records['Shipments'][0]['Consignee'] ?? '',
            "destination" => $bombino_records['Shipments'][0]['Destination'] ?? '',
            "hawb_no" => $bombino_records['Shipments'][0]['HAwbNo'] ?? '',
            "origin" => $bombino_records['Shipments'][0]['Origin'] ?? '',
            "ship_date" => date('Y-m-d H:i:s', strtotime($bombino_records['Shipments'][0]['ShipDate'])) ?? '',
            "status" => $bombino_records['Shipments'][0]['Status'],
            "weight" => $bombino_records['Shipments'][0]['Weight'] ?? '',
        ];
        $result = [];
        foreach ($records as $record) {

            $bombino_record = [
                'action_date' => date('Y-m-d', strtotime($record['ActionDate'])) ?? '',
                'action_time' => date('H:i:s', strtotime($record['ActionTime'])) ?? '',
                'event_code' => $record['EventCode'] ?? '',
                'event_detail' => $record['EventDetail'] ?? '',
                'exception' => $record['Exception'] ?? '',
                'location' => $record['Location' ?? '']
            ];
            $result[] = [...$Bombino_Data, ...$bombino_record];
        }
        Log::alert($result);
        BombinoTracking::upsert($result, ['awbno_actionDate_eventDetail_unique'], [
            'awb_no',
            'consignee',
            'consignor',
            'destination',
            'hawb_no',
            'origin',
            'ship_date',
            'weight',
            'action_date',
            'action_time',
            'event_code',
            'event_detail',
            'exception',
            'location'
        ]);
    }
}
