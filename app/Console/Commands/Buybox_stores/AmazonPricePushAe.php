<?php

namespace App\Console\Commands\Buybox_stores;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Products_ae;
use App\Models\Buybox_stores\Product_push_ae;

class AmazonPricePushAe extends Command
{
    private $increase_by_price = 1;
    private $decrease_by_price = 1;
    private $increase_by_excel_price = 5;
    private $rules_applied = [];
    private $price_calculate_type = 'fixed';
    private $our_merchant_ids = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:price_push_ae';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $products = Products_ae::query()
            ->whereBetween("updated_at", [$start_date, $end_date])
            ->where("cyclic", 1)
            ->where("cyclic_push", 0)
            ->limit(1000)
            ->get();

        if ($products->count() <= 0) {

            Products_ae::query()->update(['cyclic_push' => 0]);
            return false;
        }
        $data_to_insert = [];
        $asins = [];
        foreach ($products as $product) {

            $id_rules_applied = $product->asin . "_" . $product->store_id;
            $push_price = $this->push_price_logic($product, $id_rules_applied);

            $asins[] = $product->asin;
            // if Push is not equal to existing store price then don't push it
            if (isset($push_price) && $push_price != $product->store_price) {

                echo "selected $product->asin, $push_price \n";

                Product_push_ae::create([
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
                    'is_bb_won' => $product->is_bb_won ?? 0,
                    'applied_rules' => json_encode($this->rules_applied[$id_rules_applied]) ?? "No Rules Applied"

                ]);
            } else {
                echo $push_price . ' - ' . $product->store_price . " - " . $product->ceil_price . "\n";
                echo ('push_price is' . ' - ' . $push_price);
                // Log::notice(" ASIN: $product->asin - STORE_ID: $product->store_id - PUSH_PRICE: $push_price - BASE_PRICE: $product->base_price, STORE_PRICE: $product->store_price, BB_PRICE: $product->bb_winner_price");
            }
        }

        Products_ae::whereIn('asin', $asins)->where("store_id", $product->store_id)->update(['cyclic_push' => 1]);
    }

    public function push_price_logic($product, $id_rules_applied)
    {

        $store_id = $product->store_id;
        $base_price = $product->base_price;
        $ceil_price = $product->ceil_price;
        $store_price = $product->store_price;
        $excel_price = $product->app_360_price ?? "";
        $bb_winner_id = $product->bb_winner_id ?? "";
        $bb_winner_price = $product->bb_winner_price;
        $lowest_seller_price = $product->lowest_seller_price;
        $highest_seller_price = $product->highest_seller_price;

        if ($this->i_have_bb(store_id: $store_id, bb_winner_id: $bb_winner_id)) {

            // if we have own the BB then no otherr sellers are selling that product then we increase the price
            if (empty($highest_seller_price) && empty($lowest_seller_price)) {

                //$push_price = $this->calculate($product->store_price, 'increase');
                $push_price = $this->only_seller_excel_price_increase(excel_calculated_price: $excel_price);

                if ($push_price < $ceil_price) {

                    $this->rules_applied[$id_rules_applied] = [
                        "We have won the BB",
                        "No other sellers are selling this product so increased the price by $excel_price + $this->increase_by_excel_price %, But not more than ceil price."
                    ];

                    return $push_price;
                }
            }

            // if we have own the BB then if next highest seller is there & he is not our own seller means then we increase the price closest to the next highest price guy but not more than him
            if (!empty($highest_seller_price) && !$this->any_of_our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id)) {

                //$push_price = addPercentage($product->store_price, $this->increase_by_price);
                $push_price = $this->calculate($highest_seller_price, 'decrease');

                if ($highest_seller_price < $push_price && $push_price < $ceil_price) {

                    $this->rules_applied[$id_rules_applied] = [
                        "We have won the BB",
                        "There is next highest seller",
                        "He is not any of our seller",
                        "so we have increased the price by $this->increase_by_price & closest to the next highest seller price but not more than that price."
                    ];

                    return $push_price;
                }

                $this->rules_applied[$id_rules_applied] = [
                    "We have won the BB",
                    "There is next highest seller",
                    "He is not any of our seller",
                    "so we have increased the price to Ceil Price $ceil_price as our ceil price is reached."
                ];

                // if ceil price is breached then we add the ceil price only
                return $ceil_price;
            }

            // if we have own the BB then if next highest seller is there & he is our own store then we increase the price excel price + $this->increase_by_excel_price % but not more than ceil price
            if (!empty($highest_seller_price) && $this->any_of_our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id)) {

                //$push_price = $this->calculate($product->highest_seller_price, 'decrease');
                $push_price = $this->only_seller_excel_price_increase(excel_calculated_price: $excel_price);

                if ($push_price < $ceil_price) {

                    $our_own = $this->any_of_our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id);

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

            if ($push_price < $ceil_price) {

                $this->rules_applied[$id_rules_applied] = [
                    "We have lost the BB",
                    "No other selling this product so increased the price by $excel_price + $this->increase_by_excel_price %, But not more than ceil price",
                ];

                return $push_price;
            }
        }

        // if we have lost the BB & BB is won by any of our own sellers. then increase the price by excel price + $this->increase_by_excel_price %
        if (!empty($bb_winner_id) && $this->any_of_our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id)) {

            $push_price = $this->only_seller_excel_price_increase(excel_calculated_price: $excel_price);

            if ($push_price < $ceil_price) {

                $our_own_seller = $this->any_of_our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id);

                // Log::notice("OUR OWN SELLER");
                // Log::notice($our_own_seller);

                $this->rules_applied[$id_rules_applied] = [
                    "We have lost the BB",
                    "But any one of our own seller ($our_own_seller) has won the BB",
                    "So increased the price by $excel_price + $this->increase_by_excel_price %, But not more than ceil price",
                ];

                return $push_price;
            }
        }

        // if we have lost the BB & BB is not won by any of our own sellers but won by someday else & bb winner price is more than our store price
        if (!empty($bb_winner_id) && !$this->any_of_our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id) && $bb_winner_price > $store_price) {

            //$push_price = removePercentage($product->bb_winner_price, $this->increase_by_price);
            $push_price = $this->calculate($bb_winner_price, 'decrease');

            if ($push_price > $base_price && $push_price < $ceil_price) {

                $this->rules_applied[$id_rules_applied] = [
                    "We have lost the BB",
                    "BB has been won by ($bb_winner_id) seller",
                    "BB winner price is greater than our store price",
                    "So we have increased our price by $this->decrease_by_price than BB winner but not more than ceil price or below base price"
                ];

                return $push_price;
            }

            $this->rules_applied[$id_rules_applied] = [
                "We have lost the BB",
                "BB has been won by ($bb_winner_id) seller",
                "BB winner price is greater than our store price",
                "So we have increased our price by Ceil Price $ceil_price as our ceil price mark has reached"
            ];

            return $ceil_price;
        }

        // if we have lost the BB & BB is not won by any of our own sellers but won by someday else & bb winner has lesser price than our store price
        if (!empty($bb_winner_id) && !$this->any_of_our_own_store_won_bb(store_id: $store_id, bb_winner_id: $bb_winner_id) && $bb_winner_price < $store_price) {

            //$push_price = removePercentage($bb_winner_price, $this->increase_by_price);
            $push_price = $this->calculate($bb_winner_price, 'decrease');

            $this->info("Price reduce by 5 BB winner Price $bb_winner_price - Store Pirce $store_price - Push Price $push_price Rules applied to $id_rules_applied");

            if ($push_price > $base_price && $push_price < $ceil_price) {

                $this->rules_applied[$id_rules_applied] = [
                    "We have lost the BB",
                    "BB has been won by ($bb_winner_id) seller",
                    "BB winner price is lesser than our store price",
                    "So we decreased our price by $this->decrease_by_price than BB winner but not more than ceil price or below base price",
                ];

                return $push_price;
            }

            $this->rules_applied[$id_rules_applied] = [
                "We have lost the BB",
                "BB has been won by ($bb_winner_id) seller",
                "BB winner price is lesser than our store price",
                "So we decreased our price to Base Price $base_price as we have reached our base prices.",
            ];

            return $base_price;
        }

        $this->rules_applied[$id_rules_applied] = [
            "We have lost the BB",
            "No Condition matched",
            "So no rule applied to it & No Price Changes Made"
        ];

        return $product->store_price;
    }

    public function calculate($price, $type = 'increase')
    {

        if ($this->price_calculate_type == "percent") {

            if ($type == "increase") {
                return addPercentage_product_push($price, $this->increase_by_price);
            }

            if ($type == "decrease") {
                return removePercentage_product_push($price, $this->decrease_by_price);
            }
        }

        if ($type == "increase") {
            return $price + $this->increase_by_price;
        }

        if ($type == "decrease") {
            return $price - $this->decrease_by_price;
        }
    }

    public function only_seller_excel_price_increase($excel_calculated_price): float|int
    {

        return addPercentage_product_push($excel_calculated_price, $this->increase_by_excel_price);
    }

    public function any_of_our_own_store_won_bb(int $store_id, string $bb_winner_id)
    {

        if (in_array($bb_winner_id, $this->our_merchant_ids) && $this->our_merchant_ids[$store_id] != $bb_winner_id) {
            return $this->our_merchant_ids[$store_id];
        }

        return false;
    }

    public function i_have_bb(string $store_id, string $bb_winner_id): bool
    {
        return  $this->our_merchant_ids[$store_id] == $bb_winner_id;
    }
}
