<?php

use App\Models\User;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use Illuminate\Support\Facades\Route;
use App\Models\seller\AsinMasterSeller;
use Illuminate\Support\Facades\Storage;
use App\Models\seller\SellerAsinDetails;
use Illuminate\Support\Facades\Response;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::get('test/catalog/{asin}/{country}', 'TestController@getASIN');
Route::get('test/seller/order/{seller_id}/{country_code}', 'TestController@getSellerOrder');
Route::get('test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');
Route::get('renameamazoninvoice/', 'TestController@RenameAmazonInvoice');
Route::get('getPricing/', 'TestController@GetPricing');

Route::get('test1', function () {

    $PacketForwarder = PacketForwarder::where('status', NULL)
        ->orWhere('status', '')
        ->orWhere('status', '0')
        ->get();

    po($PacketForwarder);
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
Route::get('intouae/{weight}/{price}', 'TestController@INDToUAE');
Route::get('INDToSG/{weight}/{price}', 'TestController@INDToSG');

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
Route::get('test/order', 'TestController@testOrderAPI');
Route::get('search_catalog/{country_code}', 'TestController@searchCatalog');

Route::get('pricing', 'TestController@PricingTest');

Route::get('test/zoho', 'TestController@TestZoho');
Route::get('test/get/zoho/data/{lead}', 'TestController@TestGetZoho');

Route::get('test/smsa/booking', 'Testcontroller@SmsaBooking');

Route::get('test/amazon-feed', 'Testcontroller@TestAmazonFeed');
