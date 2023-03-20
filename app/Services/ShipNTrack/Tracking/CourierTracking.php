<?php

namespace App\Services\ShipNTrack\Tracking;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use App\Models\ShipNTrack\Aramex\AramexTrackings;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;

class CourierTracking
{
    public function AramexAPI($username, $passkey, $awbNo, $accoundId, $process_management_id)
    {
        $url = "https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc/json/TrackShipments";
        $payload =
            [
                "ClientInfo" => [
                    "UserName" => $username,
                    "Password" => $passkey,
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

        $this->AramexDataFormatting($response, $accoundId);

        $command_end_time = now();
        ProcessManagementUpdate($process_management_id, $command_end_time);
    }

    public function AramexDataFormatting($response, $accoundId)
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

        AramexTrackings::upsert($aramex_records, ['awbno_update_timestamp_description_unique'], [
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

    public function smsaAPI($username, $passKey, $awbNo, $accoundId, $process_management_id)
    {
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

        $smsa_data = $arrayResult['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram']['NewDataSet']['Tracking'];

        $smsa_records = [];
        if (isset($smsa_data[0])) {

            foreach ($smsa_data as $smsa_value) {
                $smsa_records[] = [
                    'account_id' => $accoundId,
                    'awbno' => $smsa_value['awbNo'] ?? $smsa_data['awbNo'],
                    'date' => date('Y-m-d H:i:s', strtotime($smsa_value['Date'] ?? $smsa_data['Date'])),
                    'activity' => $smsa_value['Activity'] ?? $smsa_data['Activity'],
                    'details' => $smsa_value['Details'] ?? $smsa_data['Details'],
                    'location' => $smsa_value['Location'] ?? $smsa_data['Location']
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

        SmsaTrackings::upsert($smsa_records, ['awbno_date_activity_unique'], [
            'account_id',
            'awbno',
            'date',
            'activity',
            'details',
            'location',
        ]);

        $command_end_time = now();
        ProcessManagementUpdate($process_management_id, $command_end_time);
    }
}
