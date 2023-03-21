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
use App\Services\ShipNTrack\Tracking\CourierTracking;

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

        $AramexTracking = new CourierTracking();
        $courierCodeName = ['ss_ae' => 'Smsa', 'am_ae' => 'Aramex', 'bom' => 'Bombino', 'ss_ksa' => 'Smsa', 'am_ksa' => 'Aramex'];

        $records = [];
        $awb_no = $record['awbNo'];
        $destination = strtolower($record['destination']);
        $process_management_id = $record['process_management_id'];

        if ($destination == 'ae') {

            $records = IntoAE::with(['CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4'])
                ->where('awb_number', $awb_no)
                ->get()
                ->toArray();
            // Log::alert($records);
        } else if ($destination == 'ksa') {
            $records = IntoKSA::with(['CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4'])
                ->where('awb_number', $awb_no)
                ->get()
                ->toArray();
        }
        foreach ($records as $record) {

            if ($record['forwarder_1_flag'] == 0 && $record['forwarder_1_awb'] != '') {

                $courierCode = $record['courier_partner1']['courier_code'];

                $results = [
                    'process_management' => $process_management_id,
                    'destination' => $record['courier_partner1']['destination'],
                    'account_id' => $record['courier_partner1']['id'],
                    'user_name' => $record['courier_partner1']['key1'],
                    'pass_key' => $record['courier_partner1']['key2'],
                    'awb_no' => $record['forwarder_1_awb'],
                ];

                $methodName = $courierCodeName[$courierCode] . "API";
                $AramexTracking->$methodName($results);
                $results = [];
            }
            if ($record['forwarder_2_flag'] == 0 && $record['forwarder_2_awb'] != '') {

                $courierCode = $record['courier_partner2']['courier_code'];

                $results = [
                    'process_management' => $process_management_id,
                    'destination' => $record['courier_partner2']['destination'],
                    'account_id' => $record['courier_partner2']['id'],
                    'user_name' => $record['courier_partner2']['key1'],
                    'pass_key' => $record['courier_partner2']['key2'],
                    'awb_no' => $record['forwarder_2_awb'],
                ];

                $methodName = $courierCodeName[$courierCode] . "API";
                $AramexTracking->$methodName($results);
                $results = [];
            }

            if ($record['forwarder_3_flag'] == 0 && $record['forwarder_3_awb'] != '') {

                $courierCode = $record['courier_partner3']['courier_code'];

                $results = [
                    'process_management' => $process_management_id,
                    'destination' => $record['courier_partner3']['destination'],
                    'account_id' => $record['courier_partner3']['id'],
                    'user_name' => $record['courier_partner3']['key1'],
                    'pass_key' => $record['courier_partner3']['key2'],
                    'awb_no' => $record['forwarder_3_awb'],
                ];

                $methodName = $courierCodeName[$courierCode] . "API";
                $AramexTracking->$methodName($results);
                $results = [];
            }

            if ($record['forwarder_4_flag'] == 0 && $record['forwarder_4_awb'] != '') {

                $courierCode = $record['courier_partner4']['courier_code'];

                $results = [
                    'process_management' => $process_management_id,
                    'destination' => $record['courier_partner4']['destination'],
                    'account_id' => $record['courier_partner4']['id'],
                    'user_name' => $record['courier_partner4']['key1'],
                    'pass_key' => $record['courier_partner4']['key2'],
                    'awb_no' => $record['forwarder_4_awb'],
                ];

                $methodName = $courierCodeName[$courierCode] . "API";
                $AramexTracking->$methodName($results);
                $results = [];
            }
        }
    }
}
