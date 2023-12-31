<?php

namespace App\Console\Commands\Buybox_stores;

use Exception;
use App\Models\Mws_region;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Storage;
use App\Models\order\OrderSellerCredentials;

class Asin_price_import extends Command
{

    private $base_percentage = 20;
    private $ceil_percentage = 20;
    private $price_calculate_type = "percent";

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

        $start_date = Carbon::now()->subMinutes(20);
        $end_date = Carbon::now()->subMinutes(5);

        $datas = Product::select(DB::raw("count(*), group_concat('asin', ',')"), 'store_id')
            ->where('cyclic', 0)
            ->groupBy('store_id')
            ->limit(5)
            ->get()->toArray();

        print_r($datas);
        
        exit;


        if ($datas->count() <= 0) {

            Product::where('cyclic', 1)->update(['cyclic' => 0]);

            return $this->handle();
        }

        $new_datas = $datas->groupBy('store_id');

        foreach ($new_datas as $store_id => $data) {

            $result_asins = $data->pluck('asin');

            Product::where('store_id', $store_id)->whereIn('asin', $result_asins)->update(['cyclic' => 1]);

            $country = OrderSellerCredentials::where('seller_id', $store_id)->select('country_code')->first();
            $country_code = $country->country_code;

            if ($country_code == 'IN') {
                $this->pricingin($result_asins, $store_id);
            } else if ($country_code == 'AE') {
                //$this->pricingae($result_asins, $store_id);
            } else if ($country_code == 'US') {
                $this->pricinguss($result_asins, $store_id);
            } else if ($country_code == 'SA') {
                //$this->pricinguss($result_asins, $store_id);
            } else {
                Log::notice('store_id' . $store_id . '-' . 'Country Code'. $country_code .'No pricing Logic Found');
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
            'pricing_ins.next_highest_seller_price',
            'pricing_ins.next_highest_seller_id',
            'pricing_ins.next_lowest_seller_price',
            'pricing_ins.next_lowest_seller_id',
            'pricing_ins.bb_winner_price',
            'pricing_ins.bb_winner_id',
            'pricing_ins.is_any_our_seller_won_bb'
        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');

        $data = $table_name->select($select_query)
            ->join('pricing_ins', 'asin_destination_uss.asin', '=', 'pricing_ins.asin')
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result_asins)->get();

        $insert_data_in = [];

        foreach ($data as $value) {

            //$ceil_price  = addPercentage($value['usa_to_in_b2c'], $this->base_percentage);
            //$base_price  = removePercentage($value['usa_to_in_b2c'], $this->ceil_percentage);

            $price_calculate = $this->calculate($value['usa_to_in_b2c']);

            $insert_data_in[] = [
                'store_id' => $store_id,
                'asin' => $value['asin'],
                'priority' => $value['priority'],
                'availability' => $value['available'],
                'bb_price' => ceil($value['in_price']),
                'app_360_price' => ceil($value['usa_to_in_b2c']),
                'base_price' => ceil($price_calculate['base_price']),
                'ceil_price' => ceil($price_calculate['ceil_price']),
                'lowest_seller_id' => $value['next_lowest_seller_id'],
                'lowest_seller_price' => ceil($value['next_lowest_seller_price']),
                'highest_seller_id' => $value['next_highest_seller_id'],
                'highest_seller_price' => ceil($value['next_highest_seller_price']),
                'bb_winner_id' => $value['bb_winner_id'],
                'bb_winner_price' => ceil($value['bb_winner_price']),
                'is_bb_won' => $value['is_any_our_seller_won_bb'],
                'cyclic' => 1
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
            'pricing_aes.next_highest_seller_price',
            'pricing_aes.next_highest_seller_id',
            'pricing_aes.next_lowest_seller_price',
            'pricing_aes.next_lowest_seller_id',
            'pricing_aes.bb_winner_price',
            'pricing_aes.bb_winner_id',
            'pricing_aes.is_any_our_seller_won_bb',
        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');

        $data = $table_name->select($select_query)
            ->join('pricing_aes', 'asin_destination_uss.asin', '=', 'pricing_aes.asin')
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result_asins)->get();

        $insert_data = [];

        foreach ($data as $value) {

            // $ceil_price = addPercentage($value['usa_to_uae'], $this->base_percentage);
            // $base_price = removePercentage($value['usa_to_uae'], $this->ceil_percentage);

            $price_calculate = $this->calculate($value['usa_to_uae']);

            $insert_data[] = [
                'store_id' => $store_id,
                'asin' => $value['asin'],
                'priority' => $value['priority'],
                'availability' => $value['available'],
                'bb_price' => ceil($value['ae_price']),
                'app_360_price' => ceil($value['usa_to_uae']),
                'base_price' => ceil($price_calculate['base_price']),
                'ceil_price' => ceil($price_calculate['ceil_price']),
                'lowest_seller_id' => $value['next_lowest_seller_id'],
                'lowest_seller_price' => ceil($value['next_lowest_seller_price']),
                'highest_seller_id' => $value['next_highest_seller_id'],
                'highest_seller_price' => ceil($value['next_highest_seller_price']),
                'bb_winner_id' => $value['bb_winner_id'],
                'bb_winner_price' => ceil($value['bb_winner_price']),
                'is_bb_won' => $value['is_any_our_seller_won_bb'],
                'cyclic' => 1
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
            'pricing_uss.is_any_our_seller_won_bb'
        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');

        $data = $table_name->select($select_query)
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereIn("asin_destination_uss.asin", $result_asins)->get();

        $insert_data = [];

        foreach ($data as $value) {

            /* $ceil_price = addPercentage($value['us_price'], $this->base_percentage);
            $base_price = removePercentage($value['us_price'], $this->ceil_percentage); */

            $price_calculate = $this->calculate($value['usa_to_uae']);

            $insert_data[] = [
                'asin' => $value['asin'],
                'store_id' => $store_id,
                'priority' => $value['priority'],
                'availability' => $value['available'],
                'bb_price' => ceil($value['us_price']),
                'app_360_price' => ceil($value['us_price']),
                'base_price' => ceil($price_calculate['base_price']),
                'ceil_price' => ceil($price_calculate['ceil_price']),
                'lowest_seller_id' => $value['next_lowest_seller_id'],
                'lowest_seller_price' => ceil($value['next_lowest_seller_price']),
                'highest_seller_id' => $value['next_highest_seller_id'],
                'highest_seller_price' => ceil($value['next_highest_seller_price']),
                'bb_winner_id' => $value['bb_winner_id'],
                'bb_winner_price' => ceil($value['bb_winner_price']),
                'is_bb_won' => $value['is_any_our_seller_won_bb'],
                'cyclic' => 1
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

    public function calculate($price)
    {

        if ($this->price_calculate_type == "percent") {

            $ceil_price  = addPercentage($price, $this->base_percentage);
            $base_price  = removePercentage($price, $this->ceil_percentage);

            return ['ceil_price' => $ceil_price, 'base_price' => $base_price];
        }

        $ceil_price = $price + $this->ceil_percentage;
        $base_price = $price - $this->base_percentage;

        return ['ceil_price' => $ceil_price, 'base_price' => $base_price];
    }
}
