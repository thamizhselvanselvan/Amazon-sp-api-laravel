<?php

namespace App\Console\Commands\Buybox_stores;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\PMS_SP_API\Process\FeedProcess;

class SP_API_Push extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:sp_api_push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push the data to SP-API';

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

        $product_pushs = DB::connection('buybox_stores')->table("product_push")
            ->select("push_price", 'product_sku', 'base_price', 'latency', 'store_id')
            ->get();

        if($product_pushs->isEmpty()) {

            return false;
        }    

        $previewsArrays = [];

        foreach($product_pushs as $product_push) {
            
            $previewsArrays[$product_push->store_id][] = [
                'sku' => $product_push->product_sku,
                'new_my_price' => $product_push->push_price,
                'minimum_seller_price' => $product_push->base_price
            ];
        }
        
        foreach($previewsArrays as $seller_id => $feedLists) {

            $feed = new FeedProcess;
            $feed->feedSubmit($feedLists, $seller_id);
        }
    }
}
