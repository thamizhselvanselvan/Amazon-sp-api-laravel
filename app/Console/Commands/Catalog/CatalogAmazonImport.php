<?php

namespace App\Console\Commands\Catalog;

use App\Models\Mws_region;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\NewCatalog;

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
        $sources = ['in', 'us'];
        $limit_array = ['in' => 2500, 'us' => 2500];

        foreach ($sources as $source) {
            log::notice($source);
            $limit = $limit_array[$source];

            $auth_count = 0;
            $asin_upsert_source = [];
            $seller_id = '';
            $asin_source = [];
            $count = 0;
            $queue_name = 'catalog';
            $queue_delay = 0;
            $class =  'catalog\AmazonCatalogImport';
            $asin_table_name = 'asin_source_' . $source . 's';
            $catalog_table_name = 'catalognew' . $source . 's';
            $current_data = date('H:i:s');

            $catalog_class = new NewCatalog();
            $asins = [];

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
                    AND source.status = '0'
                    LIMIT $limit 
                    ");
            }

            $country_code_up = strtoupper($source);
            $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code_up)->get()->toArray();

            // Log::info("${country_code_up} -> total asin for catalog " . count($asins));

            if ($country_code_up == 'IN') {
                $queue_name = 'catalog_IN';
            }
            if (count($asins) > 0) {

                foreach ($asins as $details) {

                    $seller_id  =  $details->user_id;

                    $asin = $details->asin;

                    $asin_upsert_source[] = [
                        'asin' => $asin,
                        'user_id' => $seller_id,
                        'status' => '1'
                    ];

                    $aws_id = $mws_regions[0]['aws_verified'][$auth_count]['id'];
                    if ($count == 20) {
                        log::alert($asin_source);
                        // jobDispatchFunc($class, $asin_source, $queue_name, $queue_delay);
                        $catalog_class->Catalog($asin_source);
                        $auth_count++;
                        $asin_source = [];
                        $count = 0;
                    }

                    if (strlen($asin) == 10) {
                        $asin_source[] = [
                            'asin'      => $asin,
                            'seller_id' => $details->user_id,
                            'source'    => $source,
                            'id'        => $aws_id,
                        ];
                        $count++;
                    }

                    if ($auth_count == 2) {
                        $auth_count = 0;
                    }
                }

                $catalog_class->Catalog($asin_source);
                // jobDispatchFunc($class, $asin_source, $queue_name, $queue_delay);

                $model = 'Asin_source';
                $table_name = "asin_source_";
                $source_mode = table_model_create($source, $model, $table_name);

                $source_mode->upsert($asin_upsert_source, ['user_asin_unique'], ['status']);
            } else {
                DB::connection('catalog')->update("UPDATE ${asin_table_name} as source 
                LEFT JOIN ${catalog_table_name} as cat 
                ON cat.asin = source.asin
                SET source.status = '0'
                WHERE cat.asin IS NULL
                AND source.status = '1'
                ");
            }
        }
    }
}
