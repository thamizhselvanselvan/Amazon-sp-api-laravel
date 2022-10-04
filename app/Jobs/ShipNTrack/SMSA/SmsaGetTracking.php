<?php

namespace App\Jobs\ShipNTrack\SMSA;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SmsaGetTracking implements ShouldQueue
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
        $this->payload =  $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $awbNo_array = $this->payload;

        foreach ($awbNo_array as $awbNo) {

            $tracking_details = [];
            $details = SmsaTrackingResponse($awbNo);

            foreach ($details as $key => $value) {

                $tracking_details[] = [

                    "awbno" => $value['awbNo'],
                    "date" => date('Y-m-d H:i:s', strtotime($value['Date'])),
                    "activity" => $value['Activity'],
                    "details" => $value['Details'],
                    "location" => $value['Location']
                ];
            }

            SmsaTrackings::upsert(
                $tracking_details,
                ['awbno_date_activity_unique'],
                ['awbno', 'date', 'activity', 'details', 'location']
            );
        }
    }
}
