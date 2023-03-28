<?php

namespace App\Services\ShipNTrack\Tracking;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\ShipNTrack\CourierTracking\BombinoTracking;
use App\Models\ShipNTrack\CourierTracking\BombinoTrackings;

class BombinoTrackingAPIServices
{
    public function Bombino($records)
    {
        $awbNo = $records['awb_no'];
        $password = $records['pass_key'];
        $user_name = $records['user_name'];
        $account_id = $records['account_id'];
        $reference_id = $records['reference_id'];
        $process_management_id = $records['process_management_id'];

        $url = "http://api.bombinoexp.in/bombinoapi.svc/Tracking?";

        $response = Http::withoutVerifying()->withHeaders([
            "Content-Type" => "application/json"
        ])->get($url . "AccountId=" . $account_id . "&UserId=" . $user_name . "&Password=" . $password . "&AwbNo=" . $awbNo);

        $this->BombinoDataFormatting($response);

        $command_end_time = now();
        ProcessManagementUpdate($process_management_id, $command_end_time);
    }

    public function BombinoDataFormatting($response)
    {
        $bombino_records = json_decode($response, true);

        $records = $bombino_records['Shipments'][0]['TrackPoints'];
        $Bombino_Data = [

            "awbno" => $bombino_records['Shipments'][0]['AWBNo'] ?? '',
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
        Log::alert("Bombino");
        Log::alert($result);

        BombinoTracking::upsert($result, ['awbno_actionDate_eventDetail_unique'], [
            'awbno',
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
