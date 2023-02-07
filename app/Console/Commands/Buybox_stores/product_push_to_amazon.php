<?php

namespace App\Console\Commands\buybox_stores;

use App\Models\Aws_credential;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Cache;
use App\Models\Buybox_stores\Product_Push;

class product_push_to_amazon extends Command
{
    private $increase_by_price = 5;
    private $decrease_by_price = 5;
    private $increase_by_excel_price = 10;
    private $rules_applied = []; 
    private $price_calculate_type = 'fixed';
    private $our_merchant_ids = [];

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
       
        $this->our_merchant_ids = aws_merchant_ids();

        $start_date = Carbon::now()->subMinutes(30);
        $end_date = Carbon::now()->subMinutes(1);

        $products = Product::query()
            ->whereBetween("updated_at", [$start_date, $end_date])
            ->where("cyclic", 1)
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

            // echo $product->asin."-".$push_price."-".$id_rules_applied;
            // echo "\n";

            Product::where('asin', $product->asin)->where("store_id", $product->store_id)->update(['cyclic_push' => 1]);
            
            // if Push is not equal to existing store price then don't push it
            if(isset($push_price) && $push_price != $product->store_price) {

                echo "selected $product->asin, $push_price \n";

              //  $data_to_insert[] = ;

                Product_Push::insert([
                    'asin' => $product->asin,
                    'store_id' =>  $product->store_id,
                    'product_sku' => $product->product_sku,
                    'availability' => $product->availability,
                    'app_360_price' => $product->app_360_price,
                    'destination_bb_price' => $product->bb_price,
                    'push_price' => ceil($push_price),
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
                echo $push_price .' - '. $product->store_price . " - " . $product->ceil_price ."\n";
               // Log::notice(" ASIN: $product->asin - STORE_ID: $product->store_id - PUSH_PRICE: $push_price - BASE_PRICE: $product->base_price, STORE_PRICE: $product->store_price, BB_PRICE: $product->bb_winner_price");
            }

        }

        
    }

    public function push_price_logic($product, $id_rules_applied) {

        $store_id = $product->store_id;
        $base_price = $product->base_price;
        $ceil_price = $product->ceil_price;
        $store_price = $product->store_price;
        $excel_price = $product->app_360_price;
        $bb_winner_id = $product->bb_winner_id ?? "";
        $bb_winner_price = $product->bb_winner_price;
        $lowest_seller_price = $product->lowest_seller_price;
        $highest_seller_price = $product->highest_seller_price;

        if ($this->i_have_bb(store_id: $store_id, bb_winner_id: $bb_winner_id)) {
            
            // if we have own the BB then no otherr sellers are selling that product then we increase the price
            if (empty($highest_seller_price) && empty($lowest_seller_price)) {

                //$push_price = $this->calculate($product->store_price, 'increase');
                $push_price = $this->only_seller_excel_price_increase(excel_calculated_price: $excel_price);
                
                if($push_price < $ceil_price) {

                    $this->rules_applied[$id_rules_applied] = [
                        "We have won the BB",
                        "No other sellers are selling this product so increased the price by $excel_price + $this->increase_by_excel_price %, But not more than ceil price"
                    ];

                    return $push_price;
                }

            } 

            // if we have own the BB then if next highest seller is there & he is not our own seller means then we increase the price closest to the next highest price guy but not more than him
            if(!empty($highest_seller_price) && !$this->our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id)) {

                //$push_price = addPercentage($product->store_price, $this->increase_by_price);
                $push_price = $this->calculate(price: $highest_seller_price, type: 'decrease');

                if($highest_seller_price < $push_price && $push_price < $ceil_price) {

                    $this->rules_applied[$id_rules_applied] = [
                        "We have won the BB",
                        "There is next highest seller",
                        "He is not any of our seller",
                        "so we have increased the price by $this->increase_by_price & closest to the next highest seller price but not more than that price"
                    ];

                    return $push_price;
                }

            }

            // if we have own the BB then if next highest seller is there & he is our own store then we increase the price excel price + $this->increase_by_excel_price % but not more than ceil price
            if(!empty($highest_seller_price) && $this->our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id)) {

                //$push_price = $this->calculate($product->highest_seller_price, 'decrease');
                $push_price = $this->only_seller_excel_price_increase(excel_calculated_price: $excel_price);

                if($push_price < $ceil_price) {

                    $our_own = $this->our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id);

                    $this->rules_applied[$id_rules_applied] = [
                        "We have won the BB",
                        "There is next highest seller",
                        "He is our own seller {$our_own}",
                        "so we have increased the price by $excel_price + $this->increase_by_excel_price %, But not more than ceil price"
                    ];

                    return $push_price;
                }

            }

            $this->rules_applied[$id_rules_applied] = [
                "We have won the BB",
                "No Condition Matched",
                "So no rule applied to it & No Price Changes Made"
            ];

            return $product->store_price;
        } 
            
        // if we have lost the BB then no other sellers are sellling that product then we increase that prices
        if (empty($bb_winner_id) && empty($highest_seller_price) && empty($lowest_seller_price)) {

            //$push_price = $this->calculate($product->store_price, 'increase');
            $push_price = $this->only_seller_excel_price_increase(excel_calculated_price: $excel_price);

            if($push_price < $ceil_price) {

                $this->rules_applied[$id_rules_applied] = [
                    "We have lost the BB",
                    "No other selling this product so increased the price by $excel_price + $this->increase_by_excel_price %, But not more than ceil price",
                ];

                return $push_price;
            }

        }

        // if we have lost the BB & BB is won by any of our own sellers.
        if (!empty($bb_winner_id) && $this->our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id)) {

            $push_price = $this->only_seller_excel_price_increase(excel_calculated_price: $excel_price);

            if($push_price < $ceil_price) {

                $our_own_seller = $this->our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id);

                $this->rules_applied[$id_rules_applied] = [
                    "We have lost the BB",
                    "But any one of our own seller ($our_own_seller) has won the BB",
                    "So increased the price by $excel_price + $this->increase_by_excel_price %, But not more than ceil price",
                ];

                return $push_price;
            }

        }

        // if we have lost the BB & BB is not won by any of our own sellers but won by someday else & bb winner price is more than our store price
        if (!empty($bb_winner_id) && !$this->our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id) && $bb_winner_price > $store_price) {

            //$push_price = removePercentage($product->bb_winner_price, $this->increase_by_price);
            $push_price = $this->calculate(price: $bb_winner_price, type: 'decrease');

            if($push_price > $base_price && $push_price < $ceil_price) {

                $this->rules_applied[$id_rules_applied] = [
                    "We have lost the BB",
                    "BB has been won by ($bb_winner_id) seller",
                    "BB winner price is greater than our store price",
                    "So we have increased our price by $this->decrease_by_price than BB winner but not more than ceil price or below base price"
                ];

                return $push_price;
            }
  
        } 

        // if we have lost the BB & BB is not won by any of our own sellers but won by someday else & bb winner has lesser price than our store price
        if(!empty($bb_winner_id) && !$this->our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id) && $bb_winner_price < $store_price) {

            //$push_price = removePercentage($bb_winner_price, $this->increase_by_price);
            $push_price = $this->calculate(price: $bb_winner_price, type: 'decrease');

            if($push_price > $base_price && $push_price < $ceil_price) {

                $this->rules_applied[$id_rules_applied] = [
                    "We have lost the BB",
                    "BB has been won by ($bb_winner_id) seller",
                    "BB winner price is lesser than our store price",
                    "So we decreased our price by $this->decrease_by_price than BB winner but not more than ceil price or below base price",
                ];

                return $push_price;
            }

        }

        $this->rules_applied[$id_rules_applied] = [
            "We have lost the BB",
            "No Condition matched",
            "So no rule applied to it & No Price Changes Made"
        ];

        return $product->store_price;
    }

    public function calculate(string|int|float $price, string $type = 'increase') {

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

    public function only_seller_excel_price_increase(string|float|int $excel_calculated_price): float|int {

        return addPercentage($excel_calculated_price, $this->increase_by_excel_price);
    }

    public function our_own_store_won_bb(int $store_id, string $bb_winner_id): bool {

        if(array_key_exists($store_id, $this->our_merchant_ids) && $store_id != $bb_winner_id) {
            return $this->our_merchant_ids[$store_id];
        }

        return false;
    }

    public function i_have_bb(string $store_id, string $bb_winner_id): bool {
        return $store_id == $bb_winner_id;
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
