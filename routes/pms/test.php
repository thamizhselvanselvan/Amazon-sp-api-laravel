<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\seller\AsinMasterSeller;
use App\Models\seller\SellerAsinDetails;
use Illuminate\Support\Facades\Response;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::get("test", function () {

    $country_code = 'uk';

    $data = DB::connection('buybox')
        ->select("SELECT 
        count(*) as cnt,
        PP.asin1,
        GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
        GROUP_CONCAT(PPO.is_fulfilled_by_amazon) as is_fulfilled_by_amazon,
        group_concat(PPO.listingprice_amount) as listingprice_amount,
        group_concat(PP.delist) as delist ,
        group_concat(PP.available) as available,
        group_concat(PPO.updated_at) as updated_at
        FROM bb_product_uks as PP
            LEFT JOIN bb_product_lp_seller_detail_uks as PPO ON PP.asin1 = PPO.asin
            Where PP.seller_id = 42
            GROUP BY PP.asin1 
            LIMIT 50
        ");

    $pricing = [];
    $asin_details = [];
    foreach ($data  as  $value) {

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
                        'asin' => $value->asin1,
                        'source' => $country_code,
                        'is_buybox_winner' => $value1,
                        'is_fulfilled_by_amazon' => $fulfilled[$key],
                        'listingprice_amount' => $listing_price[$key],
                        'delist' => $delist[$key],
                        'available' => $available[$key],
                        'updated_at' => $updated_at[$key],
                    ];
                break 1;
            } else {
                $asin_details =
                    [
                        'asin' => $value->asin1,
                        'source' => $country_code,
                        'is_buybox_winner' => $value1,
                        'is_fulfilled_by_amazon' => $fulfilled[$key],
                        'listingprice_amount' => min($listing_price),
                        'delist' => $delist[$key],
                        'available' => $available[$key],
                        'updated_at' => $updated_at[$key],
                    ];
            }
        }
        $pricing[] = $asin_details;
    }


    po($pricing);
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
