<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Buybox_stores\Product_Push;

class AmazonFeedAPICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:amazon-feed-api';

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
        $store_id = 6;
        $class = "Amazon_Feed\amazonFeedPriceAvailabilityPush";
        $queue_name = "default";
        $queue_delay = 0;

        $products = Product_Push::query()
            ->select('product_sku', 'store_id', 'availability', 'push_price', 'base_price', 'latency')
            ->where('push_status', 0)
            ->where('store_id', $store_id)
            ->get()
            ->toArray();

        $feedData = [];
        foreach ($products as $product) {

            $feedData['seller_id'] = $store_id;
            $feedData['feedLists'] = $product;
            jobDispatchFunc($class, $feedData, $queue_name, $queue_delay);
        }
    }
}
