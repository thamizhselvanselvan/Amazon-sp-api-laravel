<?php

namespace App\Console\Commands\Seller;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User;
use App\Models\seller\AsinMasterSeller;
use App\Models\seller\SellerAsinDetails;
use App\Models\UserBillingDetails;

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
            $count = 0;

            $seller_id = $user_id->bb_seller_id;
            AsinMasterSeller::where('seller_id', $seller_id)
                ->chunk($chunk, function ($data) use ($seller_id, &$count) {

                    $country_code = strtolower($data[0]['destination_1']);
                    $asin_array = [];

                    foreach ($data as $value) {
                        $a = $value['asin'];
                        $asin_array[] = "'$a'";
                    }

                    $product_lp = 'bb_product_lp_seller_detail_' . $country_code . 's';
                    $product = 'bb_product_' . $country_code . 's';
                    $product_lp_offer = 'bb_product_lp_offer' . $country_code . 's';

                    $asin = implode(',', $asin_array);

                    $data = DB::connection('buybox')
                        ->select("SELECT PP.asin1,
                GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                GROUP_CONCAT(PPO.is_fulfilled_by_amazon) as is_fulfilled_by_amazon,
                group_concat(PPO.listingprice_amount) as listingprice_amount,
                group_concat(PP.delist) as delist ,
                group_concat(PP.available) as available,
                group_concat(PPO.updated_at) as updated_at
                FROM $product as PP
                    LEFT JOIN $product_lp as PPO ON PP.asin1 = PPO.asin
                    Where PP.seller_id = $seller_id
                    AND PP.asin1 IN ($asin)
                    GROUP BY PP.asin1 
                ");
                    $pricing = [];
                    $asin_details = [];
                    $update_asin = [];
                    foreach ($data  as  $value) {

                        $update_asin[] = $value->asin1;
                        $buybox_winner = explode(',', $value->is_buybox_winner);
                        $fulfilled = explode(',', $value->is_fulfilled_by_amazon);
                        $listing_price = explode(',', $value->listingprice_amount);
                        $delist = explode(',', $value->delist);
                        $available = explode(',', $value->available);
                        $updated_at = explode(',', $value->updated_at);

                        foreach ($buybox_winner as $key => $value1) {
                            if ($value1 == '1') {
                                $asin_details =
                                    [
                                        'seller_id' => $seller_id,
                                        'asin' => $value->asin1,
                                        'source' => $country_code,
                                        'is_buybox_winner' => $value1,
                                        'is_fulfilment_by_amazon' => $fulfilled[$key],
                                        'listingprice_amount' => $listing_price[$key],
                                        'delist' => $delist[$key],
                                        'available' => $available[$key],
                                        'price_updated_at' => $updated_at[$key] ? $updated_at[$key] : NULL,
                                    ];
                                break 1;
                            } else {
                                $asin_details =
                                    [
                                        'seller_id' => $seller_id,
                                        'asin' => $value->asin1,
                                        'source' => $country_code,
                                        'is_buybox_winner' => $value1,
                                        'is_fulfilment_by_amazon' => $fulfilled[$key],
                                        'listingprice_amount' => min($listing_price),
                                        'delist' => $delist[$key],
                                        'available' => $available[$key],
                                        'price_updated_at' => $updated_at[$key] ? $updated_at[$key] : NULL,
                                    ];
                            }
                        }
                        $pricing[] = $asin_details;
                    }

                    SellerAsinDetails::upsert(
                        $pricing,
                        'seller_id_asin_unique',
                        ['seller_id', 'source', 'asin', 'is_buybox_winner', 'is_fulfilment_by_amazon', 'listingprice_amount', 'delist', 'available', 'price_updated_at']
                    );

                    $count += AsinMasterSeller::where([['seller_id', $seller_id], ['status', '0']])->whereIN('asin', $update_asin)->update(['status' => '1']);
                });

            UserBillingDetails::create([
                'seller_id' => $seller_id,
                'count' => $count,
                'module' => 'seller'
            ]);
        }
        return true;
    }
}
