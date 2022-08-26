<?php

use App\Models\User;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use Illuminate\Support\Facades\Route;
use App\Models\seller\AsinMasterSeller;
use App\Models\seller\SellerAsinDetails;
use Illuminate\Support\Facades\Response;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::get("test", function () {
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
Route::get('test/seller/order/{seller_id}/{country_code}', 'TestController@getSellerOrder');
Route::get('test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');
Route::get('renameamazoninvoice/', 'TestController@RenameAmazonInvoice');
Route::get('getPricing/', 'TestController@GetPricing');

Route::get('test1', function () {

    $asin = "'B000008J4T',
    'B00000JHQ0',
    'B00004R9W5',
    'B00004TMFE',
    'B00004Z5LR',
    'B000050B6Y',
    'B000052WTG',
    'B000052WVA',
    'B000052XB5',
    'B000052XHI',
    'B000052XIA',
    'B000052XPU',
    'B000052XZP',
    'B000052XZQ',
    'B000052XZZ',
    'B000052Y0B',
    'B000052Y0S',
    'B000052Y65',
    'B000052YAI',
    'B000052YBV'";

    $product_lp = 'bb_product_uss_lp_offers';
    $product_seller_details = 'bb_product_uss_seller_details';
    $asin_price = DB::connection('buybox')
        ->select("SELECT PPO.asin, LP.available,
GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
group_concat(PPO.listingprice_amount) as listingprice_amount,
group_concat(PPO.updated_at) as updated_at
FROM $product_seller_details as PPO
JOIN $product_lp as LP
WHERE PPO.asin = LP.asin
     AND PPO.asin IN ($asin)
    GROUP BY PPO.asin
");
    po($asin_price);
});


Route::get('ustoinb2c/{weight}/{price}', 'TestController@USAToINDb2c');
Route::get('ustoinb2b/{weight}/{price}', 'TestController@USAToINDb2b');
Route::get('ustouae/{weight}/{price}', 'TestController@USAToUAE');
Route::get('ustosg/{weight}/{price}', 'TestController@USATOSG');

Route::get('ustoinprice', function () {
    $bb_price = 9.98;
    $weight = 0.27;
    if ($weight > 0.9) {

        $int_shipping_base_charge = (6 + ($weight - 1) * 6);
    } else {

        $int_shipping_base_charge = 6;
    }
    $duty_rate = 32.00 / 100;
    $seller_commission = 10 / 100;
    $packaging = 2;
    $amazon_commission = 22.00 / 100;

    $ex_rate = 82;
    $duty_cost = round(($duty_rate * ($bb_price + $int_shipping_base_charge)), 2);

    $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
        (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $seller_commission);

    $usd_sp = round($price_befor_amazon_fees * (1 + $amazon_commission) +
        ($amazon_commission * $price_befor_amazon_fees * 0.12), 2);

    $india_sp = $usd_sp * $ex_rate;
    po($india_sp);
});

Route::get('intosa/{weight}/{price}', 'TestController@INToSA');

Route::get('smsatracking/{awb}', function ($awb_no) {


    return SmsaTrackingResponse($awb_no);

    //
});

Route::get('test/api', function () {


    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://uat-api.b2cship.us/PacificAmazonAPI.svc/TrackingAmazon',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '<?xml version="1.0" encoding="UTF-8"?>
<AmazonTrackingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
<Validation>
<UserID>Amazon</UserID>
<Password>AcZmraDzLoxA4NxLUcyrWnSiEaXxRQkfJ9B5hCbiK5M=</Password>
</Validation>
<APIVersion>1.0</APIVersion>
<TrackingNumber>US10000142</TrackingNumber>
</AmazonTrackingRequest>
',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: text/plain'
        ),
    ));

    $response = curl_exec($curl);

    if ($response === false) {
        echo 'Curl error: ' . curl_error($curl);
    }

    curl_close($curl);
    echo $response;
});
Route::get('export_catalog', 'TestController@ExportCatalog');
