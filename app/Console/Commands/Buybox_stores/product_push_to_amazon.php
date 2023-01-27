<?php

namespace App\Console\Commands\buybox_stores;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use App\Models\Buybox_stores\Product_Push;

class product_push_to_amazon extends Command
{
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
        $datas = Product::query()
            ->where(['cyclic' => '1', 'cyclic_push' => '0'])
            ->limit(100)
            ->get();
        foreach ($datas as $data) {
            $store_id = $data->store_id;
            $asin = $data->asin;
            $product_sku = $data->product_sku;
            $latency = $data->latency;
            $availability = $data->availability;
            $winner = $data->bb_winner_price;
            $bb_won = $data->is_bb_won;
            $nxt_highest_seller = $data->highest_seller_price;
            $nxt_lowest_seller = $data->lowest_seller_price;
            $push_price = 0;
            if (isset($data->ceil_price)) {
                $push_price = $data->ceil_price;
            } else if ($availability == '0') {
                $push_price = 0;
            }

            //if our store won bb
            if ($bb_won === '1' && $nxt_lowest_seller != '0' && $nxt_highest_seller != '0') {
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
            Log::notice('asin - '.$asin . ' ' .'price - '. $push_price . ' ' . 'availability -'. $availability);

            Product::where('asin', $asin)->update(['cyclic_push' => '1']);
            $data_to_insert = [
                'asin' => $asin,
                'product_sku' => $product_sku,
                'store_id' =>  $store_id,
                'availability' => $availability,
                'push_price' => $push_price,
                'base_price' => '',
                'latency' => $latency,

            ];

            Product_Push::create($data_to_insert, ['asin', 'store_id'], ['push_price', 'base_price', 'latency']);
        }
    }
}
