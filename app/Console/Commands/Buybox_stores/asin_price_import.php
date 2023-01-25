<?php

namespace App\Console\Commands\Buybox_stores;

use Exception;
use App\Models\Mws_region;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Storage;

class Asin_price_import extends Command
{

    private $base_percentage = 20;
    private $ceil_percentage = 20;

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

        $datas = Product::select('asin', 'store_id')
            ->where('cyclic', '0')
            ->orderBy('id', 'asc')
            ->limit(1000)
            ->get();

        $asins = $datas->pluck('asin');
        Product::whereIn('asin', $asins)->update(['cyclic' => '1']);


        if ($datas->count() <= 0) {

            Product::where('cyclic', '1')->update(['cyclic' => '0']);

            return $this->handle();
        }


        $new_datas = $datas->groupBy('store_id');

        foreach ($new_datas as $store_id => $data) {

            $result_asins = $data->pluck('asin');
            // Product::whereIn('asin', $result_asins)->update(['cyclic' => '5']);   //need to check after one cyclic all stores cyclic is mapping to 5

            if ($store_id == '8' || $store_id == '10' || $store_id == '27' || $store_id == '6') {

                $this->pricingin($result_asins, $store_id);
            } else if ($store_id == '7' || $store_id == '9' || $store_id == '12' || $store_id == '11' || $store_id == '20') {

                $this->pricingae($result_asins, $store_id);
            } else if ($store_id == '7' || $store_id == '9' || $store_id == '12') {

                $this->pricinguss($result_asins, $store_id);
            } else {
                Log::notice('store_id' . $store_id);
            }
        }
    }

    public function pricingin($result_asins, $store_id)
    {

        $select_query = [
            'asin_destination_uss.priority',
            'pricing_ins.in_price',
            'pricing_uss.usa_to_in_b2c',
            'pricing_uss.available',
            'pricing_uss.asin',
            'pricing_uss.next_highest_seller_price',
            'pricing_uss.next_highest_seller_id',
            'pricing_uss.next_lowest_seller_price',
            'pricing_uss.next_lowest_seller_id',
            'pricing_uss.bb_winner_price',
            'pricing_uss.bb_winner_id',
            'pricing_uss.is_any_our_seller_won_bb',

        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');

        $data = $table_name->select($select_query)
            ->join('pricing_ins', 'asin_destination_uss.asin', '=', 'pricing_ins.asin')
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result_asins)->get();

        $insert_data_in = [];

        foreach ($data as $value) {

            $base_price = addPercentage($value['in_price'], $this->base_percentage);
            $ceil_price = removePercentage($value['in_price'], $this->ceil_percentage);

            $insert_data_in[] = [
                'bb_price' => $value['in_price'],
                'app_360_price' => $value['usa_to_in_b2c'],
                'priority' => $value['priority'],
                'availability' => $value['available'],
                'base_price' => $base_price,
                'ceil_price' => $ceil_price,
                'store_id' => $store_id,
                'asin' => $value['asin'],
                'lowest_seller_id' => $value['next_lowest_seller_id'],
                'lowest_seller_price' => $value['next_lowest_seller_price'],
                'highest_seller_id' => $value['next_highest_seller_id'],
                'highest_seller_price' => $value['next_highest_seller_price'],
                'bb_winner_id' => $value['bb_winner_id'],
                'bb_winner_price' => $value['bb_winner_price'],
                'is_bb_won' => $value['is_any_our_seller_won_bb'],
                'cyclic' => '11',
            ];
        }

        $this->product_upsert($insert_data_in);
    }

    public function pricingae($result_asins, $store_id)
    {

        $select_query = [
            'asin_destination_uss.priority',
            'pricing_aes.ae_price',
            'pricing_uss.usa_to_uae',
            'pricing_uss.available',
            'pricing_uss.asin',
            'pricing_uss.next_highest_seller_price',
            'pricing_uss.next_highest_seller_id',
            'pricing_uss.next_lowest_seller_price',
            'pricing_uss.next_lowest_seller_id',
            'pricing_uss.bb_winner_price',
            'pricing_uss.bb_winner_id',
            'pricing_uss.is_any_our_seller_won_bb',
        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');

        $data = $table_name->select($select_query)
            ->join('pricing_aes', 'asin_destination_uss.asin', '=', 'pricing_aes.asin')
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result_asins)->get();

        $insert_data = [];

        foreach ($data as $value) {

            $base_price = addPercentage($value['ae_price'], $this->base_percentage);
            $ceil_price = removePercentage($value['ae_price'], $this->ceil_percentage);

            $insert_data[] = [
                'bb_price' => $value['ae_price'],
                'app_360_price' => $value['usa_to_uae'],
                'priority' => $value['priority'],
                'availability' => $value['available'],
                'base_price' => $base_price,
                'ceil_price' => $ceil_price,
                'store_id' => $store_id,
                'asin' => $value['asin'],
                'lowest_seller_id' => $value['next_lowest_seller_id'],
                'lowest_seller_price' => $value['next_lowest_seller_price'],
                'highest_seller_id' => $value['next_highest_seller_id'],
                'highest_seller_price' => $value['next_highest_seller_price'],
                'bb_winner_id' => $value['bb_winner_id'],
                'bb_winner_price' => $value['bb_winner_price'],
                'is_bb_won' => $value['is_any_our_seller_won_bb'],
                'cyclic' => '12',
            ];
        }

        $this->product_upsert($insert_data);
    }

    public function pricinguss($result_asins, $store_id)
    {

        $select_query = [
            'asin_destination_uss.priority',
            'pricing_uss.us_price',
            'pricing_uss.available',
            'pricing_uss.asin',
            'pricing_uss.next_highest_seller_price',
            'pricing_uss.next_highest_seller_id',
            'pricing_uss.next_lowest_seller_price',
            'pricing_uss.next_lowest_seller_id',
            'pricing_uss.bb_winner_price',
            'pricing_uss.bb_winner_id',
            'pricing_uss.is_any_our_seller_won_bb',

        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');

        $data = $table_name->select($select_query)
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result_asins)->get();

        $insert_data = [];

        foreach ($data as $value) {

            $base_price = addPercentage($value['us_price'], $this->base_percentage);
            $ceil_price = removePercentage($value['us_price'], $this->ceil_percentage);

            $insert_data[] = [
                'bb_price' => $value['us_price'],
                'app_360_price' => $value['us_price'],
                'priority' => $value['priority'],
                'availability' => $value['available'],
                'base_price' => $base_price,
                'ceil_price' => $ceil_price,
                'store_id' => $store_id,
                'asin' => $value['asin'],
                'lowest_seller_id' => $value['next_lowest_seller_id'],
                'lowest_seller_price' => $value['next_lowest_seller_price'],
                'highest_seller_id' => $value['next_highest_seller_id'],
                'highest_seller_price' => $value['next_highest_seller_price'],
                'bb_winner_id' => $value['bb_winner_id'],
                'bb_winner_price' => $value['bb_winner_price'],
                'is_bb_won' => $value['is_any_our_seller_won_bb'],
                'cyclic' => '13',
            ];
        }

        $this->product_upsert($insert_data);
    }

    public function product_upsert($data)
    {

        Product::upsert(
            $data,
            ['asin_store_id_unique'],
            [
                'app_360_price', 'bb_price', 'priority', 'availability', 'base_price', 'ceil_price', 'cyclic',
                'lowest_seller_id', 'lowest_seller_price', 'highest_seller_id', 'highest_seller_price', 'bb_winner_id', 'bb_winner_price', 'is_bb_won',
            ]
        );
    }
}
