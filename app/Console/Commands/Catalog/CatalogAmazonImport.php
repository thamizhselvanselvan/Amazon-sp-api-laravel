<?php

namespace App\Console\Commands\Catalog;

use App\Models\Mws_region;
use App\Models\ProcessManagement;
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
        //Process Management start
        $process_manage = [
            'module'             => 'Catalog Import',
            'description'        => 'Import Catalog from Amazon',
            'command_name'       => 'mosh:catalog-amazon-import',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        //Process Management end

        $sources = ['in', 'us', 'ae'];
        foreach ($sources as $source) {

            $limit = getSystemSettingsValue(strtolower($source) . '_catalog_limit', 1000);

            $auth_count = 0;
            $asin_upsert_source = [];
            $seller_id = '';
            $asin_source = [];
            $count = 0;
            $queue_name = 'catalog_US';
            $queue_delay = 0;
            $class =  'catalog\AmazonCatalogImport';
            $asin_table_name = 'asin_source_' . $source . 's';
            $catalog_table_name = 'catalognew' . $source . 's';
            $current_data = date('H:i:s');

            $asins = [];

            if ($current_data >= '01:00:00' && $current_data <= '01:05:00') {
                // Log::info('UnAvaliable catalog asin dump');

                // $asins = DB::connection('catalog')->select("SELECT source.asin, source.user_id
                //     FROM $asin_table_name as source
                //     LEFT JOIN $catalog_table_name as cat
                //     ON cat.asin = source.asin
                //     WHERE cat.seller_id IS NULL ");
            } else {

                // $asins = DB::connection('catalog')->select("SELECT source.asin, source.user_id
                //     FROM $asin_table_name as source
                //     LEFT JOIN $catalog_table_name as cat
                //     ON cat.asin = source.asin
                //     WHERE cat.asin IS NULL
                //     AND source.status = '0'
                //     LIMIT $limit
                //     ");

                $asins = DB::connection('catalog')->select("SELECT asin, user_id
                FROM $asin_table_name
                WHERE status='0'
                ORDER BY id DESC
                LIMIT $limit
                ");
            }

            $country_code_up = strtoupper($source);
            $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code_up)->get()->toArray();

            if ($country_code_up == 'IN') {
                $queue_name = 'catalog_IN';
            } else if ($country_code_up == 'AE') {
                $queue_name = 'default';
            }

            if (count($asins) > 0) {

                $model = 'Asin_source';
                $table_name = "asin_source_";
                $source_mode = table_model_create($source, $model, $table_name);

                foreach ($asins as $details) {
                    $seller_id  =  $details->user_id;
                    $asin = $details->asin;

                    $asin_upsert_source[] = [
                        'asin' => $asin,
                        'user_id' => $seller_id,
                        'status' => '2'
                    ];

                    $aws_id = $mws_regions[0]['aws_verified'][$auth_count]['id'] ?? 0;

                    if ($count == 10) {

                        jobDispatchFunc($class, $asin_source, $queue_name, $queue_delay);
                        $source_mode->upsert($asin_upsert_source, ['user_asin_unique'], ['status']);
                        $auth_count++;
                        $asin_source = [];
                        $asin_upsert_source = [];
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

                jobDispatchFunc($class, $asin_source, $queue_name, $queue_delay);
                $source_mode->upsert($asin_upsert_source, ['user_asin_unique'], ['status']);
                $asin_upsert_source = [];
                $asin_source = [];
            } else {

                // DB::connection('catalog')->update("UPDATE ${asin_table_name} as source
                // LEFT JOIN ${catalog_table_name} as cat
                // ON cat.asin = source.asin
                // SET source.status = '0'
                // WHERE cat.asin IS NULL
                // AND source.status = '1'
                // ");
            }
        }

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
