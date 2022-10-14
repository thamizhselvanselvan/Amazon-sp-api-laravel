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
    protected $signature = 'pms:asin-import {user_id} {--source=} {path}';

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
        $sources = explode(',', $this->option('source'));

        $path = $this->argument('path');
        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        $csv_data = [];
        foreach ($sources as $source) {

            foreach ($csv as $data) {
                $csv_data[] = $data['ASIN'];
            }

            $chunk_data = [];
            $chunk = array_chunk($csv_data, 30000);
            $class = "catalog\ImportAsinSourceDestinationCsvFile";
            $queue_name = "csv_import";
            $delay = 0;
            Log::notice("Enter inside command");
            log::alert($chunk);
            foreach ($chunk as $value) {

                $chunk_data  = [

                    'ASIN'      =>  $value,
                    'user_id'   =>  $user_id,
                    'source'   =>  $source
                ];

                jobDispatchFunc($class, $chunk_data, $queue_name, $delay);
                log::alert($chunk_data);

                Log::notice('chunk happening');
            }
            $csv_data = [];
        }
    }
}
