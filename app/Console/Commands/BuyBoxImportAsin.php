<?php

namespace App\Console\Commands;

use League\Csv\Reader;
use App\Services\BB\PushAsin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BuyBoxImportAsin extends Command
{
    private $destination;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:buybox-import-asin {--columns=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Asin into BuyBox table according to priority and source';

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
        $column_data = $this->option('columns');
        $final_data = [];
        $destination = '';
        $explode_array = explode(',', $column_data);

        foreach ($explode_array as $key => $value) {
            list($key, $value) = explode('=', $value);
            $final_data[$key] = $value;
            if ($key == 'destination') {
                $des = $value;
                $destination = str_replace('_', ',', $des);
            }
        }
        // Log::alert($destination);
        $file_management_id = $final_data['fm_id'];
        $user_id = $final_data['user_id'];
        $path = $final_data['path'];
        $priority = $final_data['priority'];
        $destinations = explode(',', $destination);

        $asins = Reader::createFromPath(Storage::path($path), 'r');
        $asins->setHeaderOffset(0);
        $count = 0;
        $asin = [];
        $csv_priority = [];
        foreach ($destinations as $this->destination) {

            foreach ($asins as $asin_details) {
                $asin[] = $asin_details['ASIN'];
                $csv_priority[] = $asin_details['Priority'] == 1 ? 1 : 0;
            }

            $chunk_data = [];
            $asin_chunk = array_chunk($asin, 5000);
            $priority_chunk = array_chunk($csv_priority, 5000);
            $class = "catalog\ImportAsinSourceDestinationCsvFile";
            $queue_name = "csv_import";
            $delay = 0;
            $count = 0;
            $asin_chunk_count = count($asin_chunk) - 1;
            // log::notice($asin_chunk);
            foreach ($asin_chunk as $key => $value) {
                $chunk_data = [
                    'ASIN'          => $value,
                    'user_id'       => $user_id,
                    'source'        => $this->destination,
                    'priority'      =>  $priority_chunk[$key],
                    'fm_id'         =>  $file_management_id,
                    'module'        =>  'BuyBox',
                    'tablePriority' => $priority
                ];

                if ($count == $asin_chunk_count) {
                    // LAST CHUNK
                    $chunk_data = [
                        'ASIN'          => $value,
                        'user_id'       => $user_id,
                        'source'        => $this->destination,
                        'priority'      =>  $priority_chunk[$key],
                        'fm_id'         =>  $file_management_id,
                        'module'        =>  'BuyBox',
                        'tablePriority' => $priority,
                        'Last_queue'    => now(),
                    ];
                    jobDispatchFunc($class, $chunk_data, $queue_name, $delay);
                }

                jobDispatchFunc($class, $chunk_data, $queue_name, $delay);
                $count++;
            }
            $asin = [];
            $csv_priority = [];
        }
    }
}
