<?php

namespace App\Console\Commands\Catalog;

use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Console\Command;
use App\Models\Catalog\AsinSource;
use App\Services\BB\PushAsin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class asinBulkImport extends Command
{
    private $country;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:asin-import {user_id} {--country_code=} {path} {fm_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Asin from CSV';

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

        $user_id = $this->argument('user_id');
        $sources = explode(',', $this->option('country_code'));
        $path = $this->argument('path');
        $file_management_id = $this->argument('fm_id');

        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        $csv_data = [];
        foreach ($sources as $source) {

            foreach ($csv as $data) {
                $csv_data[] = $data['ASIN'];
            }

            $chunk_data = [];
            $chunk = array_chunk($csv_data, 5000);
            $class = "catalog\ImportAsinSourceDestinationCsvFile";
            $queue_name = "csv_import";
            $delay = 0;
            $count = 0;

            $asin_chunk_count = count($chunk) - 1;

            foreach ($chunk as $value) {

                $chunk_data  = [

                    'ASIN'      =>  $value,
                    'user_id'   =>  $user_id,
                    'source'    =>  $source,
                    'module'    =>  'source',
                    'fm_id'     =>  $file_management_id
                ];

                if ($count == $asin_chunk_count) {
                    //LAST CHUNK

                    $chunk_data  = [
                        'ASIN'      =>  $value,
                        'user_id'   =>  $user_id,
                        'source'    =>  $source,
                        'module'    =>   'source',
                        'fm_id'     =>  $file_management_id,
                        'Last_queue' =>  now(),
                    ];
                    jobDispatchFunc($class, $chunk_data, $queue_name, $delay);
                }

                jobDispatchFunc($class, $chunk_data, $queue_name, $delay);
                $count++;
            }
            $csv_data = [];
        }
    }
}
