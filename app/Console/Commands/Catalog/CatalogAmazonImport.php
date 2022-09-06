<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CatalogAmazonImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-amazon-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Catalog Amazon Import Queue';

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
        $sources = ['us', 'in'];
        $limit_array = ['in' => 2000, 'us' => 2000];

        foreach ($sources as $source) {

            $limit = $limit_array[$source];

            $asin_source = [];
            $count = 0;
            $queue_name = 'catalog';
            $queue_delay = 1;
            $class =  'catalog\AmazonCatalogImport';
            $asin_table_name = 'asin_source_' . $source . 's';
            $catalog_table_name = 'catalognew' . $source . 's';
            $current_data = date('H:i:s');
            if ($current_data >= '01:00:00' && $current_data <= '01:05:00') {
                // Log::info('UnAvaliable catalog asin dump');

                // $asins = DB::connection('catalog')->select("SELECT source.asin, source.user_id
                //     FROM $asin_table_name as source
                //     LEFT JOIN $catalog_table_name as cat
                //     ON cat.asin = source.asin
                //     WHERE cat.seller_id IS NULL ");
            } else {

                $asins = DB::connection('catalog')->select("SELECT source.asin, source.user_id 
                    FROM $asin_table_name as source
                    LEFT JOIN $catalog_table_name as cat
                    ON cat.asin = source.asin
                    WHERE cat.asin IS NULL 
                    LIMIT $limit 
                    ");
            }
            // Log::alert($asins);

            // exit;
            $country_code_up = strtoupper($source);
            if ($country_code_up == 'IN') {
                $queue_name = 'catalog_IN';
            }

            foreach ($asins as $asin) {
                if ($count == 20) {
                    jobDispatchFunc($class, $asin_source, $queue_name, $queue_delay);
                    $asin_source = [];
                    $count = 0;
                }
                $asin_source[] = [
                    'asin' => $asin->asin,
                    'seller_id' => $asin->user_id,
                    'source' => $source,
                ];
                $count++;
            }
            jobDispatchFunc($class, $asin_source, $queue_name, $queue_delay);
        }
    }
}