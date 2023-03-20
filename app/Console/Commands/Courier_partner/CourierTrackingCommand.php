<?php

namespace App\Console\Commands\Courier_partner;

use Illuminate\Console\Command;
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
    protected $description = 'Command description';

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
        $class = "ShipNTrack\Tracking\CouriersTrackingJob";
        $queue_name = "tracking";
        $destinations = ['ae', 'ksa'];
        foreach ($destinations as $destination) {
            $records = [];
            if ($destination == 'ae') {

                $records = IntoAE::with(['CourierPartner1', 'CourierPartner2'])
                    ->orWhere('forwarder_1_flag', 0)
                    ->orWhere('forwarder_2_flag', 0)
                    ->get()
                    ->toArray();
            } else {
                $records = IntoKSA::with(['CourierPartner1', 'CourierPartner2'])
                    ->orWhere('forwarder_1_flag', 0)
                    ->orWhere('forwarder_2_flag', 0)
                    ->get()
                    ->toArray();
            }

            if (count($records) > 0) {

                foreach ($records as $record) {
                    if ($record['forwarder_1_flag'] == 0) {

                        $data = [
                            'awbNo' => $record['awb_number'],
                            'destination' => $record['courier_partner1']['destination']
                        ];
                        jobDispatchFunc($class, $data, $queue_name);
                    } elseif ($record['forwarder_2_flag'] == 0) {

                        $data = [
                            'awbNo' => $record['awb_number'],
                            'destination' => $record['courier_partner1']['destination']
                        ];
                        jobDispatchFunc($class, $data, $queue_name);
                    }
                }
            }
        }
    }
}
