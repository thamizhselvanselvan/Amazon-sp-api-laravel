<?php

namespace App\Services\ShipNTrack\Tracking;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Http;
use App\Models\ShipNTrack\CourierTracking\AramexTracking;
use App\Models\ShipNTrack\CourierTracking\AramexTrackings;


class AramexTrackingAPIServices
{
    public function Aramex($records)
    {
        $awbNo = $records['awb_no'];
        $passKey = $records['pass_key'];
        $username = $records['user_name'];
        $time_zone = $records['time_zone'];
        $reference_id = $records['reference_id'];
        $process_management_id = $records['process_management_id'];

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

        $this->AramexDataFormatting($response, $reference_id, $time_zone);

        $command_end_time = now();
        ProcessManagementUpdate($process_management_id, $command_end_time);
    }

    public function AramexDataFormatting($response, $reference_id, $time_zone)
    {
        $aramex_records = [];
        $aramex_data = isset(json_decode($response, true)['TrackingResults'][0]['Value']) ? json_decode($response, true)['TrackingResults'][0]['Value'] : [];

        foreach ($aramex_data as $key1 => $aramex_value) {

            if ($aramex_value['UpdateDescription'] == 'Delivered') {
            }
            foreach ($aramex_value as $key2 => $value) {

                $aramex_records[$key1]['account_id'] = $reference_id;
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
                    $dt->setTimeZone(new DateTimeZone($time_zone));
                    $date = $dt->format('Y-m-d H:i:s');

                    $aramex_records[$key1][$key2] = $date;
                } else {

                    $aramex_records[$key1][$key2] = $value;
                }
            }
        }
        // Log::notice($aramex_records);
        // Log::notice($destination);

        AramexTracking::upsert($aramex_records, ['awbno_update_timestamp_description_unique'], [
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
