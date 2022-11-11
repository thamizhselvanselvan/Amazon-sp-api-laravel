<?php

namespace App\Console\Commands\Catalog;

use League\Csv\Reader;
use App\Services\BB\PushAsin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Catalog\AsinDestination;
use Illuminate\Support\Facades\Storage;

class AsinDestinationUpload extends Command
{
    private $destination;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Asin-destination-upload {user_id} {priority} {--country_code=} {path} {fm_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asin Destination File Upload';

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
        $push_to_bb = new PushAsin();
        $user_id = $this->argument('user_id');
        $priority = $this->argument('priority');
        $destinations = explode(',', $this->option('country_code'));
        $path = $this->argument('path');
        $file_management_id = $this->argument('fm_id');
        $asins = Reader::createFromPath(Storage::path($path), 'r');
        $asins->setHeaderOffset(0);

        $source = buyboxCountrycode();
        $Asin_record = [];
        $product = [];
        $product_lowest_price = [];
        $count = 0;
        $asin = [];
        foreach ($destinations as $this->destination) {

            foreach ($asins as $asin_details) {
                $asin[] = $asin_details['ASIN'];
            }

            $chunk_data = [];
            $asin_chunk = array_chunk($asin, 5000);
            $class = "catalog\ImportAsinSourceDestinationCsvFile";
            $queue_name = "csv_import";
            $delay = 0;
            $count = 0;
            $asin_chunk_count = count($asin_chunk) - 1;

            log::warning($asin_chunk_count);
            foreach ($asin_chunk as $value) {

                $chunk_data = [
                    'ASIN'      => $value,
                    'user_id'   => $user_id,
                    'source'    => $this->destination,
                    'module'    => 'destination',
                    'priority'  =>  $priority,
                    'fm_id'     =>  $file_management_id
                ];

                if ($count == $asin_chunk_count) {
                    // LAST CHUNK
                    log::warning($count);
                    $chunk_data = [
                        'ASIN'      => $value,
                        'user_id'   => $user_id,
                        'source'    => $this->destination,
                        'module'    => 'destination',
                        'priority'  =>  $priority,
                        'fm_id'     =>  $file_management_id,
                        'Last_queue' => now(),
                    ];
                    jobDispatchFunc($class, $chunk_data, $queue_name, $delay);
                }

                jobDispatchFunc($class, $chunk_data, $queue_name, $delay);
                $count++;
            }
            // log::alert($chunk_data);
            $asin = [];
        }






        // foreach ($destinations as $this->destination) {

        //     foreach ($asins as  $asin_details) {
        //         $asin = $asin_details['ASIN'];

        //         $Asin_record[] = [
        //             'asin'  => $asin,
        //             'user_id'   => $user_id,
        //             'priority' => $priority,

        //         ];

        //         $product[] = [
        //             'seller_id' => $source[$this->destination],
        //             'active' => 1,
        //             'asin1' => $asin,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ];

        //         $product_lowest_price[] = [
        //             'asin' => $asin,
        //             'cyclic' => 0,
        //             'priority'  => $priority,
        //             'import_type' => 'Seller',
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ];

        //         if ($count == 999) {

        //             $table_name = table_model_create(country_code: $this->destination, model: 'Asin_destination', table_name: 'asin_destination_');
        //             $table_name->upsert($Asin_record, ['user_asin_unique'], ['asin', 'user_id', 'priority']);
        //             $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $this->destination, priority: $priority);

        //             $Asin_record = [];
        //             $product = [];
        //             $product_lowest_price = [];
        //             $count = 0;
        //         }
        //         $count++;
        //     }
        //     $table_name = table_model_create(country_code: $this->destination, model: 'Asin_destination', table_name: 'asin_destination_');
        //     $table_name->upsert($Asin_record, ['user_asin_unique'], ['asin', 'user_id', 'priority']);
        //     $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $this->destination, priority: $priority);
        // }
    }
}
