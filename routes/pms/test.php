<?php

use App\Models\Catalog\Asin_master;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\seller\AsinMasterSeller;
use App\Models\seller\SellerAsinDetails;
use Illuminate\Support\Facades\Response;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::get("test", function () {

    $country_code = 'uk';

    $source = buyboxCountrycode();

    $chunk = 10;

    foreach ($source as $country_code => $seller_id) {

        $country_code_lr = strtolower('US');

        $product_lp = 'bb_product_lp_seller_detail_' . $country_code_lr . 's';
        $product = 'bb_product_' . $country_code_lr . 's';

        Asin_master::where('source', $country_code)
            ->chunk($chunk, function ($data) use ($seller_id, $country_code, $product_lp) {

                foreach ($data as $value) {
                    $a = $value['asin'];
                    $asin_array[] = "'$a'";
                }

                $asin = implode(',', $asin_array);
                $asin_price = DB::connection('buybox')
                    ->select("SELECT PPO.asin,
                GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                group_concat(PPO.listingprice_amount) as listingprice_amount,
                group_concat(PPO.updated_at) as updated_at
                FROM $product_lp as PPO
                    WHERE PPO.asin IN ($asin)
                    GROUP BY PPO.asin
                ");

                $pricing = [];
                $asin_details = [];
                $update_asin = [];
                $pricing = [];

                foreach ($asin_price as $value) {

                    $buybox_winner = explode(',', $value->is_buybox_winner);
                    $listing_price = explode(',', $value->listingprice_amount);
                    $updated_at = explode(',', $value->updated_at);

                    foreach ($buybox_winner as $key =>  $value1) {

                        if ($value1 == '1') {
                            $asin_details =
                                [
                                    'seller_id' => $seller_id,
                                    'asin' => $value->asin,
                                    'source' => $country_code,
                                    'listingprice_amount' => $listing_price[$key],
                                    'price_updated_at' => $updated_at[$key] ? $updated_at[$key] : NULL,
                                ];
                            break 1;
                        } else {

                            $asin_details =
                                [
                                    'seller_id' => $seller_id,
                                    'asin' => $value->asin,
                                    'source' => $country_code,
                                    'listingprice_amount' => min($listing_price),
                                    'price_updated_at' => $updated_at[$key] ? $updated_at[$key] : NULL,
                                ];
                        }
                    }
                    $pricing[] = $asin_details;
                }
                po($pricing);
                echo "<hr>";
            });
    }

    $pricing = [];
    $asin_details = [];

    $pricing[] = $asin_details;
    // po($pricing);
    exit;
    $awb_no = 'US10000141';
    $bombino_account_id = config('database.bombino_account_id');
    $bombino_user_id = config('database.bombino_user_id');
    $bombino_password = config('database.bombino_password');

    $url = "http://api.bombinoexp.in/bombinoapi.svc/Tracking?AccountId=$bombino_account_id&UserId=$bombino_user_id&Password=$bombino_password&AwbNo=$awb_no";

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response);
    po($response);
});

Route::get('test/catalog/{asin}/{country}', 'TestController@getASIN');
Route::get('test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');
Route::get('renameamazoninvoice/', 'TestController@RenameAmazonInvoice');

Route::get('test1', function () {
});
