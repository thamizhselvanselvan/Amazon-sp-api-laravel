<?php

namespace App\Jobs\ShipNTrack\Tracking;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;
use App\Models\ShipNTrack\ForwarderMaping\IntoKSA;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Services\ShipNTrack\Tracking\CourierTracking;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class CouriersTrackingJob implements ShouldQueue
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
        $record = $this->payload;

        $records = [];
        $awb_no = $record['awbNo'];
        $destination = strtolower($record['destination']);
        $process_management_id = $record['process_management_id'];

        if ($destination == 'ae') {

            $records = Trackingae::with(
                [
                    'CourierPartner1.courier_names',
                    'CourierPartner2.courier_names',
                    'CourierPartner3.courier_names',
                    'CourierPartner4.courier_names'
                ]
            )
                ->where('awb_number', $awb_no)
                ->get()
                ->toArray();
        } else if ($destination == 'in') {

            $records = Trackingin::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awb_no)
                ->get()
                ->toArray();
        } else if ($destination == 'ksa') {

            $records = Trackingksa::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awb_no)
                ->get()
                ->toArray();
        }

        foreach ($records as $record) {

            if ($record['forwarder_1_flag'] == 0 && $record['forwarder_1_awb'] != '') {

                $results = [
                    'awb_no' => $record['forwarder_1_awb'],
                    'process_management_id' => $process_management_id,
                    'reference_id' => $record['courier_partner1']['id'],
                    'user_name' => $record['courier_partner1']['user_id'],
                    'pass_key' => $record['courier_partner1']['password'],
                    'account_id' => $record['courier_partner1']['account_id'],
                    'time_zone' => $record['courier_partner1']['time_zone'],
                    'key1' => $record['courier_partner1']['key1'],
                    'key2' => $record['courier_partner1']['key2'],
                ];

                $trackingAPI_name = $record['courier_partner1']['courier_names']['courier_name'];
                $Instance = ServicesClass(services_path: 'ShipNTrack\Tracking', services_class: $trackingAPI_name . "TrackingAPIServices");
                $Instance->$trackingAPI_name($results);

                $results = [];
            }

            if ($record['forwarder_2_flag'] == 0 && $record['forwarder_2_awb'] != '') {

                $results = [
                    'awb_no' => $record['forwarder_2_awb'],
                    'process_management_id' => $process_management_id,
                    'reference_id' => $record['courier_partner2']['id'],
                    'user_name' => $record['courier_partner2']['user_id'],
                    'pass_key' => $record['courier_partner2']['password'],
                    'account_id' => $record['courier_partner2']['account_id'],
                    'time_zone' => $record['courier_partner2']['time_zone'],
                    'key1' => $record['courier_partner2']['key1'],
                    'key2' => $record['courier_partner2']['key2'],
                ];

                $trackingAPI_name = $record['courier_partner2']['courier_names']['courier_name'];
                $Instance = ServicesClass(services_path: 'ShipNTrack\Tracking', services_class: $trackingAPI_name . "TrackingAPIServices");
                $Instance->$trackingAPI_name($results);
                $results = [];
            }

            if ($record['forwarder_3_flag'] == 0 && $record['forwarder_3_awb'] != '') {

                $results = [
                    'awb_no' => $record['forwarder_3_awb'],
                    'process_management_id' => $process_management_id,
                    'reference_id' => $record['courier_partner3']['id'],
                    'user_name' => $record['courier_partner3']['user_id'],
                    'pass_key' => $record['courier_partner3']['password'],
                    'account_id' => $record['courier_partner3']['account_id'],
                    'time_zone' => $record['courier_partner3']['time_zone'],
                    'key1' => $record['courier_partner3']['key1'],
                    'key2' => $record['courier_partner3']['key2'],
                ];

                $trackingAPI_name = $record['courier_partner3']['courier_names']['courier_name'];
                $Instance = ServicesClass(services_path: 'ShipNTrack\Tracking', services_class: $trackingAPI_name . "TrackingAPIServices");
                $Instance->$trackingAPI_name($results);
                $results = [];
            }

            if ($record['forwarder_4_flag'] == 0 && $record['forwarder_4_awb'] != '') {

                $results = [
                    'awb_no' => $record['forwarder_4_awb'],
                    'process_management_id' => $process_management_id,
                    'reference_id' => $record['courier_partner4']['id'],
                    'user_name' => $record['courier_partner4']['user_id'],
                    'pass_key' => $record['courier_partner4']['password'],
                    'account_id' => $record['courier_partner4']['account_id'],
                    'time_zone' => $record['courier_partner4']['time_zone'],
                    'key1' => $record['courier_partner4']['key1'],
                    'key2' => $record['courier_partner4']['key2'],
                ];

                $trackingAPI_name = $record['courier_partner4']['courier_names']['courier_name'];
                $Instance = ServicesClass(services_path: 'ShipNTrack\Tracking', services_class: $trackingAPI_name . "TrackingAPIServices");
                $Instance->$trackingAPI_name($results);
                $results = [];
            }
        }
    }
}
