<?php

namespace App\Console\Commands\Buybox_stores;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Buybox_stores\Product;

class Product_fetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:product_fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Data from products & push the data to new table';

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
        $percent = true;
        $percent_price = 5;
        $fixed_price = 1;

        $products = Product::query()
            ->where("updated_at", ">=", Carbon::now()->subHour())
            ->where("cyclic_push", 0)
            ->get();

        if($products->isEmpty()) {
            return false;
        }   

        foreach($products as $product) {

            if($product->is_bb_own == 1 && empty($product->lowest_seller_price) && empty($product->highest_seller_price)) {

                $price_check = ($percent) ? addPercentage($product->bb_price, $percent_price) : $product->bb_price + $fixed_price;

                if($price_check <= $product->ceil_price) {

                    DB::connection('buybox_stores')
                    ->table("product_push")
                    ->insert([
                        "push_price" => $price_check,
                        "product_sku" => $product->product_sku,
                        "base_price" => $product->base_price,
                        "latency" => $product->latency,
                        "store_id" => $product->store_id
                    ]);

                    return true;
                }

            }

            if($product->is_bb_own == 1 && !empty($product->highest_seller_price) && $product->highest_seller_price > $product->bb_price) {

                $price_check = ($percent) ? addPercentage($product->bb_price, $percent_price) : $product->bb_price + $fixed_price;

                if($price_check <= $product->ceil_price) {

                    DB::connection('buybox_stores')
                    ->table("product_push")
                    ->update([
                        "push_price" => $price_check,
                        "product_sku" => $product->product_sku,
                        "base_price" => $product->base_price,
                        "latency" => $product->latency,
                        "store_id" => $product->store_id
                    ]);

                    return true;
                }

            }

            if($product->is_bb_own == 0 && !empty($product->bb_winner_price) && $product->bb_winner_price > $product->bb_price) {

                $price_check = ($percent) ? addPercentage($product->bb_price, $percent_price) : $product->bb_price + $fixed_price;

                if($price_check <= $product->ceil_price) {

                    DB::connection('buybox_stores')
                    ->table("product_push")
                    ->update([
                        "push_price" => $price_check,
                        "product_sku" => $product->product_sku,
                        "base_price" => $product->base_price,
                        "latency" => $product->latency,
                        "store_id" => $product->store_id
                    ]);

                    return true;
                }
                
            }

            if($product->is_bb_own == 0 && !empty($product->bb_winner_price) && $product->bb_winner_price < $product->bb_price) {

                $price_check = ($percent) ? removePercentage($product->bb_price, $percent_price) : $product->bb_price - $fixed_price;

                if($price_check >= $product->base_price) {

                    DB::connection('buybox_stores')
                    ->table("product_push")
                    ->update([
                        "push_price" => $price_check,
                        "product_sku" => $product->product_sku,
                        "base_price" => $product->base_price,
                        "latency" => $product->latency,
                        "store_id" => $product->store_id
                    ]);

                    return true;
                }
                
            }
            
        }

        foreach($products as $d) {
           // $d->cyclic_push = 1;
            $d->save();
        }


    }
}
