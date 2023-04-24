<?php

namespace App\Services\ShipNTrack\Tracking;

use Illuminate\Support\Facades\Http;
use App\Models\ShipNTrack\CourierTracking\BombinoTracking;

class BombinoTrackingAPI
{
    public function index($records)
    {
        $account_id = "58925";
        $user_id = "58925MBM";
        $password = "123";
        $awbNo = "930703346";
        $url = "http://api.bombinoexp.in/bombinoapi.svc/Tracking?";

        $response = Http::withoutVerifying()->withHeaders([
            "Content-Type" => "application/json"
        ])->get($url . "AccountId=" . $account_id . "&UserId=" . $user_id . "&Password=" . $password . "&AwbNo=" . $awbNo);

        $bombino_records = json_decode($response, true);
        po(($bombino_records));

        $records = $bombino_records['Shipments'][0]['TrackPoints'];
        $Bombino_Data = [

            "awb_no" => $bombino_records['Shipments'][0]['AWBNo'] ?? '',
            "consignee" => $bombino_records['Shipments'][0]['Consignee'] ?? '',
            "destination" => $bombino_records['Shipments'][0]['Destination'] ?? '',
            "hawb_no" => $bombino_records['Shipments'][0]['HAwbNo'] ?? '',
            "origin" => $bombino_records['Shipments'][0]['Origin'] ?? '',
            "ship_date" => date('Y-m-d H:i:s', strtotime($bombino_records['Shipments'][0]['ShipDate'])) ?? '',
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
