<?php

namespace App\Console\Commands\Buybox_stores;

use Exception;
use Illuminate\Console\Command;
use App\Models\Mws_region;
use App\Models\buybox_sotres\product;
use Illuminate\Support\Facades\Storage;

class asin_price_import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:price_priority_import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fill prce and availability from destination and pricing table';

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

        $datas = product::select('asin', 'store_id')
            ->where('cyclic', '0')
            ->orderBy('id', 'asc')
            ->limit(1000)
            ->get();

        if (count($datas) == 0) {
            product::where('cyclic', '1')->update(['cyclic' => '0']);
        }

        foreach ($datas as $data) {
            // product::where('asin', $data->asin)->update(['cyclic' => '5']);   //need to check after one cyclic all stores cyclic is mapping to 5
            $store_id = $data->store_id;
            $asin = $data->asin;
            $result[] = $asin;
            if ($store_id == '8' || $store_id == '10' || $store_id == '27') {

                $this->pricingin($result, $store_id);
            } else   if ($store_id == '7' || $store_id == '9' || $store_id == '12') {

                $this->pricingae($result, $store_id);
            } else if ($store_id == '7' || $store_id == '9' || $store_id == '12') {

                $this->pricinguss($result, $store_id);
            }
        }
    }
    public function pricingin($result, $store_id)
    {

        $select_query = [
            'asin_destination_uss.priority',
            'pricing_ins.in_price',
            'pricing_uss.usa_to_in_b2c',
            'pricing_uss.available',
            'pricing_uss.asin'
        ];
        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');
        $table_name->select($select_query)
            ->join('pricing_ins', 'asin_destination_uss.asin', '=', 'pricing_ins.asin')
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result)
            ->chunk(5000, function ($data) use ($result, $store_id) {

                $insert_data_in = [];

                foreach ($data as $value) {
                    $perc = ((($value['in_price']) / 100) * 20);
                    $base_price = $value['in_price'] +  $perc;
                    $ceil_price = $value['in_price'] - $perc;
                    $insert_data_in[] = [
                        'bb_price' => $value['in_price'],
                        'app_360_price' => $value['usa_to_in_b2c'],
                        'priority' => $value['priority'],
                        'availability' => $value['available'],
                        'base_price' => $base_price,
                        'ceil_price' => $ceil_price,
                        'store_id' => $store_id,
                        'asin' => $value['asin'],
                        'cyclic' => '1',
                    ];
                }

                product::upsert(
                    $insert_data_in,
                    ['asin_store_id_unique'],
                    ['app_360_price', 'bb_price', 'priority', 'availability', 'base_price', 'ceil_price', 'cyclic']
                );
            });
    }

    public function pricingae($result, $store_id)
    {
        $select_query = [
            'asin_destination_uss.priority',
            'pricing_aes.ae_price',
            'pricing_uss.usa_to_uae',
            'pricing_uss.available',
            'pricing_uss.asin'
        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');
        $table_name->select($select_query)
            ->join('pricing_aes', 'asin_destination_uss.asin', '=', 'pricing_aes.asin')
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result)
            ->chunk(5000, function ($data) use ($store_id) {
                $insert_data = [];

                foreach ($data as $value) {
                    $perc = ((($value['ae_price']) / 100) * 20);
                    $base_price = $value['ae_price'] + $perc;
                    $ceil_price = $value['ae_price'] - $perc;
                    $insert_data[] = [
                        'bb_price' => $value['ae_price'],
                        'app_360_price' => $value['usa_to_uae'],
                        'priority' => $value['priority'],
                        'availability' => $value['available'],
                        'base_price' => $base_price,
                        'ceil_price' => $ceil_price,
                        'store_id' => $store_id,
                        'asin' => $value['asin'],
                        'cyclic' => '1',
                    ];
                }
                product::upsert(
                    $insert_data,
                    ['asin_store_id_unique'],
                    ['priority', 'bb_price', 'app_360_price', 'availability', 'base_price', 'ceil_price', 'cyclic']
                );
            });
    }

    public function pricinguss($result, $store_id)
    {
        $select_query = [
            'asin_destination_uss.priority',
            'pricing_uss.us_price',
            'pricing_uss.available',
            'pricing_uss.asin'
        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');
        $table_name->select($select_query)
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result)
            ->chunk(5000, function ($data) use ($store_id) {

                $insert_data = [];
                foreach ($data as $value) {
                    $perc = ((($value['us_price']) / 100) * 20);
                    $base_price = $value['us_price'] + $perc;
                    $ceil_price = $value['us_price'] - $perc;
                    $insert_data[] = [
                        'bb_price' => $value['us_price'],
                        'app_360_price' => $value['us_price'],
                        'priority' => $value['priority'],
                        'availability' => $value['available'],
                        'base_price' => $base_price,
                        'ceil_price' => $ceil_price,
                        'store_id' => $store_id,
                        'asin' => $value['asin'],
                        'cyclic' => '1',
                    ];
                }
                product::upsert(
                    $insert_data,
                    ['asin_store_id_unique'],
                    ['bb_price', 'app_360_price', 'priority', 'availability', 'base_price', 'ceil_price',  'cyclic']
                );
            });
    }
}
