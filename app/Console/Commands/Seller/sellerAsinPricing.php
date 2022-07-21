<?php

namespace App\Console\Commands\Seller;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User;
use App\Models\seller\AsinMasterSeller;
use App\Models\seller\SellerAsinDetails;

class sellerAsinPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:seller-asin-get-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Asin Buy Box price';

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
        $bb_user = User::where('bb_seller_id', '!=', NULL)->get('bb_seller_id');
        $chunk = 5000;
        foreach ($bb_user as $user_id) {

            $seller_id = $user_id->bb_seller_id;
            AsinMasterSeller::where('seller_id', $seller_id)->chunk($chunk, function ($data) use ($seller_id) {
                $country_code = strtolower($data[0]['source']);
                $asin = [];

                foreach ($data as $value) {
                    $a = $value['asin'];
                    $asin[] = "'$a'";
                }

                $product_lp = 'bb_product_lp_seller_detail_' . $country_code . 's';
                $product = 'bb_product_' . $country_code . 's';

                $asin = implode(',', $asin);
                $product_details = DB::connection('buybox')
                    ->select("SELECT 
                        PPO.asin, PPO.is_fulfilled_by_amazon, PPO.listingprice_amount
                        from $product as PP 
                        join $product_lp as PPO
                        ON PP.asin1 = PPO.asin
                        WHERE PP.asin1 IN ($asin)
                        AND PP.seller_id = $seller_id AND PPO.is_buybox_winner = 1
                        ");

                $asin_pricing = [];
                foreach ($product_details as $key => $product_value) {
                    $asin_pricing[] = [
                        'seller_id' => $seller_id,
                        'asin' => $product_value->asin,
                        'is_fulfilment_by_amazon' => $product_value->is_fulfilled_by_amazon,
                        'price' => $product_value->listingprice_amount,
                    ];
                }
                SellerAsinDetails::upsert(
                    $asin_pricing,
                    'seller_id_asin_unique',
                    ['seller_id', 'asin', 'is_fulfilment_by_amazon', 'price', 'status']
                );
                // po($asin_pricing);
            });
        }

        return true;
    }
}
