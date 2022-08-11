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

        $response = BombinoTrackingResponse($awb_no);
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
