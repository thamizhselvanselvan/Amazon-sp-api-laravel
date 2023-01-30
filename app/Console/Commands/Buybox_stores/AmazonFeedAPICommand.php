<?php

namespace App\Console\Commands\Buybox_stores;

use Illuminate\Console\Command;
use App\Models\Buybox_stores\Product_Push;
use Illuminate\Cache\RateLimiting\Limit;

class AmazonFeedAPICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:amazon-feed-api {store_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from stores product_push table';

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
        $store_id = $this->argument('store_id');

        print("Store ID: ". $store_id);

        $products = Product_Push::query()
            ->select('id', 'product_sku', 'store_id', 'availability', 'push_price', 'base_price', 'latency')
            ->where('push_status', 0)
            ->where('store_id', $store_id)
            ->limit(10)
            ->get()
            ->toArray();

        if(!$products) {
            return false;
        }    

        $availability_update = [
            "seller_id" => $store_id
        ];
        $price_and_availability_update = [
            "seller_id" => $store_id
        ];
        
        foreach ($products as $product) {

            if($product['availability']) {

                $price_and_availability_update["feedLists"][] = $product;
                $price_and_availability_update["availability"] = 1;

            } else {

                $availability_update["feedLists"][] = $product;
                $availability_update["availability"] = 0;
            }

        }

        jobDispatchFunc("Amazon_Feed\AmazonFeedPriceAvailabilityPush", $availability_update);
        jobDispatchFunc("Amazon_Feed\AmazonFeedPriceAvailabilityPush", $price_and_availability_update);
    }
}
