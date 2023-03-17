<?php

namespace App\Console\Commands\Courier_partner;

use Illuminate\Console\Command;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;

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
        $class = "ShipNTrack\Tracking\CouriersTracking";
        $queue_name = "tracking";

        $records = IntoAE::with(['CourierPartner1', 'CourierPartner2'])
            ->orWhere('forwarder_1_flag', 0)
            ->orWhere('forwarder_2_flag', 0)
            ->get()
            ->toArray();

        if (count($records) > 0) {

            foreach ($records as $record) {
                if ($record['forwarder_1_flag'] == 0) {

                    po($record['awb_number']);
                    $awb_no = $record['awb_number'];
                    jobDispatchFunc($class, $awb_no, $queue_name);
                } elseif ($record['forwarder_2_flag'] == 0) {

                    po($record['awb_number']);
                    $awb_no = $record['awb_number'];
                    jobDispatchFunc($class, $awb_no, $queue_name);
                }
            }
        }
    }
}
