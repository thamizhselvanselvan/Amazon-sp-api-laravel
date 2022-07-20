<?php

namespace App\Jobs\ShipNTrack\Bombino;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\ShipNTrack\Bombino\BombinoTracking;
use App\Models\ShipNTrack\Bombino\BombinoTrackingDetails;

class BombinoGetTracking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $awb_no = $this->payload['awb_no'];
        $bombino_account_id = config('database.bombino_account_id');
        $bombino_user_id = config('database.bombino_user_id');
        $bombino_password = config('database.bombino_password');

        $url = "http://api.bombinoexp.in/bombinoapi.svc/Tracking?AccountId=$bombino_account_id&UserId=$bombino_user_id&Password=$bombino_password&AwbNo=$awb_no";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

        $result = $response->Shipments[0];
        if ($result->AWBNo != $awb_no) {

            BombinoTracking::upsert(
                ['awbno' => $awb_no, 'status' => $result->AWBNo],
                'awbno',
                ['awbno', 'status']
            );
        }

        $awb_summary = [
            'awbno' => $awb_no,
            'consignee' => $result->Consignee,
            'destination' => $result->Destination,
            'forwarding_no' => $result->ForwardingNo,
            'hawb_no' => $result->HAwbNo,
            'origin' => $result->Origin,
            'ship_date' =>  date('Y-m-d H:i:s', strtotime($result->ShipDate)),
            'status' => $result->Status,
            'weight' => $result->Weight,
        ];

        BombinoTracking::upsert(
            $awb_summary,
            'awbno',
            ['awbno', 'consignee', 'destination', 'forwarding_no', 'hawb_no', 'origin', 'ship_date', 'status', 'weight']
        );

        $awb_details = [];
        foreach ($result->TrackPoints as $key => $value) {

            $awb_details[]  = [
                'awbno' => $awb_no,
                'action_date' =>  date('Y-m-d', strtotime($value->ActionDate)),
                'action_time' => date('H:i:s', strtotime($value->ActionTime)),
                'event_code' => $value->EventCode,
                'event_details' => $value->EventDetail,
                'exception' => $value->Exception,
                'location' => $value->Location,
            ];
        }

        BombinoTrackingDetails::upsert(
            $awb_details,
            'awbno_action_date_exeption_unique',
            ['awbno', 'action_date', 'action_time', 'event_code', 'event_details', 'exception', 'location']
        );
    }
}
