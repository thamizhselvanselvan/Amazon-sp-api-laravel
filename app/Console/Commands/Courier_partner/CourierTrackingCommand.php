<?php

namespace App\Console\Commands\Courier_partner;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;
use App\Models\ShipNTrack\ForwarderMaping\IntoKSA;

class CourierTrackingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:courier-tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Coureir partner tracking command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $process_manage = [
            'module'             => 'ShipnTrack',
            'description'        => 'Coureir Partner Tracking',
            'command_name'       => 'mosh:courier-tracking',
            'command_start_time' => now(),
        ];
        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $class = "ShipNTrack\Tracking\CouriersTrackingJob";
        $queue_name = "tracking";
        $destinations = ['ae', 'ksa'];
        foreach ($destinations as $destination) {
            $records = [];
            if ($destination == 'ae') {

                $records = IntoAE::with(['CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4'])
                    ->orWhere('forwarder_1_flag', 0)
                    ->orWhere('forwarder_2_flag', 0)
                    ->orWhere('forwarder_3_flag', 0)
                    ->orWhere('forwarder_4_flag', 0)
                    ->get()
                    ->toArray();
            } else if ($destination == 'ksa') {

                $records = IntoKSA::with(['CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4'])
                    ->orWhere('forwarder_1_flag', 0)
                    ->orWhere('forwarder_2_flag', 0)
                    ->orWhere('forwarder_3_flag', 0)
                    ->orWhere('forwarder_4_flag', 0)
                    ->get()
                    ->toArray();
            }

            if (count($records) > 0) {

                foreach ($records as $record) {

                    if ($record['forwarder_1_flag'] == 0) {

                        $data = [
                            'awbNo' => $record['awb_number'],
                            'destination' => $record['courier_partner1']['destination'],
                            'process_management_id' => $pm_id
                        ];
                        jobDispatchFunc($class, $data, $queue_name);
                    } elseif ($record['forwarder_2_flag'] == 0) {

                        $data = [
                            'awbNo' => $record['awb_number'],
                            'destination' => $record['courier_partner2']['destination'],
                            'process_management_id' => $pm_id
                        ];
                        jobDispatchFunc($class, $data, $queue_name);
                    } elseif ($record['forwarder_3_flag'] == 0) {
                        $data = [
                            'awbNo' => $record['awb_number'],
                            'destination' => $record['courier_partner3']['destination'],
                            'process_management_id' => $pm_id
                        ];
                        jobDispatchFunc($class, $data, $queue_name);
                    } else if ($record['forwarder_4_flag'] == 0) {
                        $data = [
                            'awbNo' => $record['awb_number'],
                            'destination' => $record['courier_partner4']['destination'],
                            'process_management_id' => $pm_id
                        ];
                        jobDispatchFunc($class, $data, $queue_name);
                    }
                }
            }
        }
    }
}
