<?php

namespace App\Console\Commands\Buybox_stores;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use App\Models\Buybox_stores\Products_in;
use com\zoho\crm\api\record\Products;
use App\Models\order\OrderSellerCredentials;

class app360_price_import_in extends Command
{
    private $base_percentage = 20;
    private $ceil_percentage = 30;
    private $price_calculate_type = "percent";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:app360:price_import_in';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets Price from app360 to Stores products table for IN';

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

        $this->pricingin();

        return true;
    }

    public function pricingin() //$result_asins, $store_id
    {

        $start_date = Carbon::now()->subMinutes(10);
        $end_date = Carbon::now()->subMinutes(5);

        $select_query = [
            //   'buybox_stores.products_ins.store_id',
            'asin_destination_uss.priority',
            'pricing_ins.in_price',
            'pricing_uss.usa_to_in_b2b',
            'pricing_uss.available',
            'pricing_uss.asin',
            'pricing_ins.next_highest_seller_price',
            'pricing_ins.next_highest_seller_id',
            'pricing_ins.next_lowest_seller_price',
            'pricing_ins.next_lowest_seller_id',
            'pricing_ins.bb_winner_price',
            'pricing_ins.bb_winner_id',
            'pricing_ins.is_any_our_seller_won_bb',
        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');

        $data = $table_name->select($select_query)
            ->join('pricing_ins', 'asin_destination_uss.asin', '=', 'pricing_ins.asin')
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            // ->join("buybox_stores.products_ins", "buybox_stores.products_ins.asin", "=", "pricing_ins.asin")
            ->whereBetween("pricing_ins.updated_at", [$start_date, $end_date])
            ->get()->toArray();

        $insert_data_in = [];
        $asins = [];

        $total = 2000;
        $tagger = 0;
        $counter = 1;

        foreach ($data as $value) {

            //$ceil_price  = addPercentage($value['usa_to_in_b2b'], $this->base_percentage);
            //$base_price  = removePercentage($value['usa_to_in_b2b'], $this->ceil_percentage);

            $price_calculate = $this->calculate($value['usa_to_in_b2b']);

            $insert_data_in[$tagger][] = [
                'asin' => $value['asin'],
                'priority' => $value['priority'],
                'availability' => $value['available'],
                'bb_price' => ceil($value['in_price']),
                'app_360_price' => ceil($value['usa_to_in_b2b']),
                'base_price' => ceil($price_calculate['base_price']),
                'ceil_price' => ceil($price_calculate['ceil_price']),
                'lowest_seller_id' => $value['next_lowest_seller_id'],
                'lowest_seller_price' => ceil($value['next_lowest_seller_price']),
                'highest_seller_id' => $value['next_highest_seller_id'],
                'highest_seller_price' => ceil($value['next_highest_seller_price']),
                'bb_winner_id' => $value['bb_winner_id'],
                'bb_winner_price' => ceil($value['bb_winner_price']),
                'is_bb_own' => $value['is_any_our_seller_won_bb'],
                'cyclic' => 1
            ];

            $asins[$tagger][] = $value['asin'];

            if ($counter == $total) {
                $tagger++;
                $counter = 1;
            }

            $counter++;
        }


        // foreach($asins as $asin) {

        //     $datas = Products_in::select('store_id')
        //         ->whereIn('asin', $asin)
        //         ->limit(500)
        //         ->get()->toArray();

        //     foreach($datas as $data) {



        //     }


        // }

        $this->product_upsert($insert_data_in);

        //if (count($asins) > 0) {
        //where('store_id', $store_id)->
        //Products_in::whereIn('asin', $asins)->update(['cyclic' => 1]);
        // }
    }

    public function product_upsert($datas)
    {

        foreach ($datas as $data) {


            foreach ($data as $dat) {

                Products_in::where("asin", $dat['asin'])->update($dat);
            }

            // Products_in::upsert(
            //     $data,
            //     ['asin_store_id_unique'],
            //     [
            //         'app_360_price', 'bb_price', 'priority', 'availability', 'base_price', 'ceil_price', 'cyclic',
            //         'lowest_seller_id', 'lowest_seller_price', 'highest_seller_id', 'highest_seller_price', 'bb_winner_id', 'bb_winner_price', 'is_bb_own',
            //     ]
            // );

        }
    }

    public function calculate($price)
    {

        if ($this->price_calculate_type == "percent") {

            $base_price  = removePercentage($price, $this->base_percentage);
            $ceil_price  = addPercentage($price, $this->ceil_percentage);

            return ['ceil_price' => $ceil_price, 'base_price' => $base_price];
        }

        $ceil_price = $price + $this->ceil_percentage;
        $base_price = $price - $this->base_percentage;

        return ['ceil_price' => $ceil_price, 'base_price' => $base_price];
    }
}
