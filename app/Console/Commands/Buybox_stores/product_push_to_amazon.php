<?php

namespace App\Console\Commands\buybox_stores;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use App\Models\Buybox_stores\Product_Push;

class product_push_to_amazon extends Command
{
    private $increase_by_percent = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:product_push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command will take asin from stores_product table and calculate price and save it on product push table';

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
        $products = Product::query()
            ->where(['cyclic' => 1, 'cyclic_push' => 0])
            ->limit(100)
            ->get();

        $data_to_insert = [];    

        foreach ($products as $product) {

            $push_price = $this->push_price_logic($product);

            Product::where('asin', $product->asin)->update(['cyclic_push' => 1]);
            
            // if Push is not equal to existing store price then don't push it
            if($push_price != $product->store_price) {

                $data_to_insert[] = [
                    'asin' => $product->asin,
                    'product_sku' => $product->product_sku,
                    'store_id' =>  $product->store_id,
                    'push_availability_status' => $product->availability,
                    'push_price' => $push_price,
                    'base_price' => $product->base_price,
                    'latency' => $product->latency,
                    'current_store_price' => $product->store_price,
                    'bb_winner_price' => $product->bb_winner_price,

                ];

            }

        }

        Product_Push::create($data_to_insert);
    }

    public function push_price_logic($product): string {

        if ($product->is_bb_won) {
            
            // if we have own the BB then no otherr sellers are selling that product then we increase the price
            if (empty($product->highest_seller_price) && empty($product->lowest_seller_price)) {

                $push_price = addPercentage($product->bb_price, $this->increase_by_percent);

                if($product->push_price < $product->ceil_price) {
                    return $push_price;
                }

                return $product->bb_price;
            } 

            // if we have own the BB then if next highest seller is there then increase the price closest to the next highest price guy but not more than him
            if(!empty($product->highest_seller_price)) {

                $push_price = addPercentage($product->bb_price, $this->increase_by_percent);

                if($product->highest_seller_price > $push_price && $push_price < $product->ceil_price) {
                    return $push_price;
                }

                return $product->bb_price;
            }

        } else {
            
            // if we have lost the BB then no other sellers are sellling that product then we increase that prices
            if (empty($product->bb_winner_id) && empty($product->highest_seller_price) && empty($product->lowest_seller_price)) {

                $push_price = addPercentage($product->bb_price, $this->increase_by_percent);

                if($product->push_price < $product->ceil_price) {
                    return $push_price;
                }

                return $product->bb_price;
            } 

            // if we have lost the BB then if BB has been won by somebody then check  
            if (!empty($product->bb_winner_id)) {

                if($product->bb_winner_price > $product->bb_price) {

                    $push_price = removePercentage($product->bb_winner_price, $this->increase_by_percent);

                    if($push_price > $product->base_price && $push_price < $product->ceil_price) {
                        return $push_price;
                    }

                    return $product->bb_price;
                }

                if($product->bb_winner_price < $product->bb_price) {

                    $push_price = removePercentage($product->bb_winner_price, $this->increase_by_percent);

                    if($push_price > $product->base_price && $push_price < $product->ceil_price) {
                        return $push_price;
                    }

                    return $product->bb_price;
                }

                return $product->bb_price;
            } 

        }

        return $product->bb_price;
    }

    public function push_price_logic_old($data): array {

        $push_price = 0;
        $store_id = $data->store_id;
        $asin = $data->asin;
        $product_sku = $data->product_sku;
        $latency = $data->latency;
        $availability = $data->availability;
        $winner = $data->bb_winner_price;
        $bb_won = $data->is_bb_won;
        $nxt_highest_seller = $data->highest_seller_price;
        $nxt_lowest_seller = $data->lowest_seller_price;
        $base_price = $data->base_price;
        $store_price = $data->store_price;

        if (isset($data->ceil_price)) {
            $push_price = $data->ceil_price;
        } else if ($availability == 0) {
            $push_price = 0;
        }

        //if our store won bb
        if ($bb_won === 1 && $nxt_lowest_seller != 0 && $nxt_highest_seller != 0) {
            $diffrence = $nxt_highest_seller - $winner;
            $push_price =  $winner + $diffrence - 1;

            if ($push_price > $data->ceil_price) {
                $push_price = $winner;
                //bb won but price > others(no changes keeyp Stay)
            } else if ($winner > $nxt_highest_seller) {
                $push_price = $winner;
            }
            //bb lost (decrese our price(winner price - 1))
        } else if ($bb_won === '0') {
            // $push_price = $winner - 1;
            if ($push_price > $data->ceil_price) {

                $push_price = $data->ceil_price;
            } else if ($winner === '0') {
                $push_price = $data->ceil_price;
            } else if ($data->ceil_price == null) {
                $push_price = 0;
            }
            //no competitors we won BB
        } else if ($nxt_highest_seller == '0' && $nxt_lowest_seller == '0' && $bb_won === '1') {
            $push_price = $data->ceil_price;
            //bb lost but no competitors, increase to ceil
        } else if ($nxt_highest_seller == '0' && $nxt_lowest_seller == '0' && $bb_won === '0') {
            $push_price = $data->ceil_price;
        }

        return [];
    }



}
