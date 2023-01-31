<?php

namespace App\Console\Commands\buybox_stores;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use App\Models\Buybox_stores\Product_Push;

class product_push_to_amazon extends Command
{
    private $increase_by_price = 5;
    private $decrease_by_price = 5;
    private $rules_applied = []; 
    private $price_calculate_type = 'fixed';

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

        $start_date = Carbon::now()->subMinutes(30);
        $end_date = Carbon::now()->subMinutes(1);

        $products = Product::query()
            ->whereBetween("updated_at", [$start_date, $end_date])
            ->where("cyclic_push", 0)
            ->limit(1000)
            ->get();

        Log::debug($products->count() . " PRODUCT PUSH COUNT");   

        if($products->count() <= 0) {
            Log::notice(" Product to Product Push is empty");

            Product::query()->update(['cyclic_push' => 0]);
            return false;
        }    

        $data_to_insert = [];    

        foreach ($products as $product) {

            $id_rules_applied = $product->asin."_".$product->store_id;

            $push_price = $this->push_price_logic($product, $id_rules_applied);

            echo $product->asin."-".$push_price."-".$id_rules_applied;
            echo "\n";

            Product::where('asin', $product->asin)->where("store_id", $product->store_id)->update(['cyclic_push' => 1]);
            
            // if Push is not equal to existing store price then don't push it
            if(isset($push_price) && $push_price != $product->store_price) {

                echo "selected $product->asin, $push_price \n";

              //  $data_to_insert[] = ;

                Product_Push::insert([
                    'asin' => $product->asin,
                    'product_sku' => $product->product_sku,
                    'store_id' =>  $product->store_id,
                    'availability' => $product->availability,
                    'app_360_price' => $product->app_360_price,
                    'destination_bb_price' => $product->bb_price,
                    'push_price' => $push_price,
                    'base_price' => $product->base_price,
                    'ceil_price' => $product->ceil_price,
                    'latency' => $product->latency,
                    'current_store_price' => $product->store_price,
                    'lowest_seller_id' => $product->lowest_seller_id,
                    'lowest_seller_price' => $product->lowest_seller_price,
                    'highest_seller_id' => $product->highest_seller_id,
                    'highest_seller_price' => $product->highest_seller_price,
                    'bb_winner_id' => $product->bb_winner_id,
                    'bb_winner_price' => $product->bb_winner_price,
                    'is_bb_won' => $product->is_bb_won,
                    'applied_rules' => json_encode($this->rules_applied[$id_rules_applied]) ?? "No Rules Applied",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            } else {
               // Log::notice(" ASIN: $product->asin - STORE_ID: $product->store_id - PUSH_PRICE: $push_price - BASE_PRICE: $product->base_price, STORE_PRICE: $product->store_price, BB_PRICE: $product->bb_winner_price");
            }

        }

        
    }

    public function push_price_logic($product, $id_rules_applied) {

        if ($product->is_bb_won) {
            
            // if we have own the BB then no otherr sellers are selling that product then we increase the price
            if (empty($product->highest_seller_price) && empty($product->lowest_seller_price)) {

                //$push_price = addPercentage($product->store_price, $this->increase_by_price);
                $push_price = $this->calculate($product->store_price, 'increase');
                
                if($push_price < $product->ceil_price) {

                    $this->rules_applied[$id_rules_applied] = [
                        'if we have own the BB then no otherr sellers are selling that product then we increase the price',
                        "BB Price: $product->store_price, Increase Percent: $this->increase_by_price, Push Price: $push_price, Ceil Price: $product->ceil_price"
                    ];

                    return $push_price;
                }

            } 

            // if we have own the BB then if next highest seller is there then increase the price closest to the next highest price guy but not more than him
            if(!empty($product->highest_seller_price)) {

                //$push_price = addPercentage($product->store_price, $this->increase_by_price);
                $push_price = $this->calculate($product->highest_seller_price, 'decrease');

                if($product->highest_seller_price > $push_price && $push_price < $product->ceil_price) {

                    $this->rules_applied[$id_rules_applied] = [
                        'if we have own the BB then if next highest seller is there then increase the price closest to the next highest price guy but not more than him',
                        "BB Price: $product->store_price, Increase Percent: $this->increase_by_price, Push Price: $push_price, 
                        Ceil Price: $product->ceil_price, Highest Seller Price: $product->highest_seller_price"
                    ];

                    return $push_price;
                }

            }

            $this->rules_applied[$id_rules_applied] = [
                'No Rule applied to it',
                'if we have own the BB then no otherr sellers are selling that product then we increase the price',
                "BB Price: $product->store_price, Increase Percent: $this->increase_by_price, Push Price: $push_price, Ceil Price: $product->ceil_price"
            ];

            return $product->store_price;

        } else {
            
            // if we have lost the BB then no other sellers are sellling that product then we increase that prices
            if (empty($product->bb_winner_id) && empty($product->highest_seller_price) && empty($product->lowest_seller_price)) {

                //$push_price = addPercentage($product->store_price, $this->increase_by_price);
                $push_price = $this->calculate($product->store_price, 'increase');

                if($push_price < $product->ceil_price) {

                    $this->rules_applied[$id_rules_applied] = [
                        'if we have lost the BB then no other sellers are sellling that product then we increase that prices',
                        "BB Price: $product->store_price, Increase Percent: $this->increase_by_price, Push Price: $push_price, 
                        Ceil Price: $product->ceil_price, Highest Seller Price: $product->highest_seller_price"
                    ];

                    return $push_price;
                }

            } 

            // if we have lost the BB then if BB has been won by somebody then check  
            if (!empty($product->bb_winner_id)) {

                if($product->bb_winner_price > $product->store_price) {

                    //$push_price = removePercentage($product->bb_winner_price, $this->increase_by_price);
                    $push_price = $this->calculate($product->bb_winner_price, 'decrease');

                    if($push_price > $product->base_price && $push_price < $product->ceil_price) {

                        $this->rules_applied[$id_rules_applied] = [
                            'if we have lost the BB then if BB has been won by somebody then check, if BB winner price is greater than our price.',
                            "BB Price: $product->store_price, Increase Percent: $this->increase_by_price, Push Price: $push_price, 
                            Ceil Price: $product->ceil_price, Highest Seller Price: $product->highest_seller_price, Base Price: $product->base_price"
                        ];

                        return $push_price;
                    }

                }

                if($product->bb_winner_price < $product->store_price) {

                    //$push_price = removePercentage($product->bb_winner_price, $this->increase_by_price);
                    $push_price = $this->calculate($product->bb_winner_price, 'decrease');

                    if($push_price > $product->base_price && $push_price < $product->ceil_price) {

                        $this->rules_applied[$id_rules_applied] = [
                            'if we have lost the BB then if BB has been won by somebody then check, if BB winner price is lesser than our price.',
                            "BB Price: $product->store_price, Increase Percent: $this->increase_by_price, Push Price: $push_price, 
                            Ceil Price: $product->ceil_price, Highest Seller Price: $product->highest_seller_price, Base Price: $product->base_price"
                        ];

                        return $push_price;
                    }

                }

                $this->rules_applied[$id_rules_applied] = [
                    'No Rule applied to it',
                    'if we have own the BB then no otherr sellers are selling that product then we increase the price',
                    "BB Price: $product->store_price, Increase Percent: $this->increase_by_price, Push Price: $push_price, Ceil Price: $product->ceil_price"
                ];

                return $product->store_price;
            } 

        }

        $this->rules_applied[$id_rules_applied] = [
            'No Rule applied to it',
            'if we have own the BB then no otherr sellers are selling that product then we increase the price',
            "BB Price: $product->store_price, Increase Percent: $this->increase_by_price, Push Price: $push_price, Ceil Price: $product->ceil_price"
        ];

        return $product->store_price;
    }

    public function calculate($price, $type = 'increase') {

        if($this->price_calculate_type == "percent") {

            if($type == "increase") {
                return addPercentage($price, $this->increase_by_price);
            }

            if($type == "decrease") {
                return removePercentage($price, $this->decrease_by_price);
            }
        
        } 

        if($type == "increase") {
            return $price + $this->increase_by_price;
        }

        if($type == "decrease") {
            return $price - $this->decrease_by_price;
        }
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
