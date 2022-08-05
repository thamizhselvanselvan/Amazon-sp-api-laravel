<?php

namespace App\Console\Commands\Shipntrack\TrackingEventMaster;

use League\Csv\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\ShipNTrack\EventMaster\TrackingEventMaster;

class UploadTrackingEventMasterCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:tracking-event-master {courier_partner}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload csv file for Tracking event master';

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
       
        $courier_partner = $this->argument('courier_partner');
        Log::notice($courier_partner);
        $file_path = "CSV/import/trackingEventMaster.csv";

        $reader = Reader::createFromPath(Storage::path(($file_path), 'r'));
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();
        $eventmaster_data = [];
        
        foreach($records as $record)
        {   
            
            $eventmaster_data [] = [
                "event_code" => $record['TrackingEventCode'],
                "description" => htmlspecialchars($record['EventCodeDescription']),
                "active" => $record['IsActive'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }   
         
        TrackingEventMaster::upsert($eventmaster_data, ['event_code']);
        
    }
}
