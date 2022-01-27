<?php

use ClouSale\AmazonSellingPartnerAPI\Models\MerchantFulfillment\Length;
use Hamcrest\Arrays\IsArray;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/spapitest', 'viewPageController@spapitest')->name('spapi');

Route::get('/', function () {
    return view('welcome');
});
/*
    CatlogApi
*/
Route::get('/view', 'CatlogApiController@index')->name('show');
Route::post('/showInput', 'CatlogApiController@show')->name(('showInput'));

/*
    productPricing
    ->CompetitivePricing
*/
Route::get('/pricing', 'productPricing\CompetitivePricingController@index')->name('productPricing.getCompetitivePricing');
Route::get('/getPrice', 'productPricing\CompetitivePricingController@show')->name('getPrice');

/* productPricing
    ->Itemoffers
*/
Route::get('/itemoffer', 'productPricing\ItemOfferController@index')->name('productPricing.show');
Route::post('/getItemOffer', 'productPricing\ItemOfferController@show')->name('getItemOffer');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/test/getCatalogItem', [App\Http\Controllers\TestController::class, 'getCatalogItem'])->name('getCatalogItem');
Route::get('/test/getCompetitivePricing', [App\Http\Controllers\TestController::class, 'getCompetitivePricing'])->name('getCompetitivePricing');
Route::get('/test/getItemOffers', [App\Http\Controllers\TestController::class, 'getItemOffers'])->name('getItemOffers');
Route::get('/test/getPricing', [App\Http\Controllers\TestController::class, 'getPricing'])->name('getPricing');

Route::get('/info', function () {
    phpinfo();
});

Route::get('/tests', function () {
});



Route::get('/test', function () {

    $url = "https://uat-api.b2cship.us/PacificAmazonAPI.svc/TrackingAmazon";

    $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
<AmazonTrackingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
<Validation>
<UserID>Amazon</UserID>
<Password>AcZmraDzLoxA4NxLUcyrWnSiEaXxRQkfJ9B5hCbiK5M=</Password>
</Validation>
<APIVersion>1.0</APIVersion>
<TrackingNumber>US10000115</TrackingNumber>
</AmazonTrackingRequest>';

    //setting the curl headers
    $headers = array(
        "Content-type: text/plain ;charset=\"utf-8\"",
        "Accept: application/plain",
    );

    try {

        $ch = curl_init();

        //setting the curl options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $xmlRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);
        $ofset= 0;
        $newArray = [];
        //convert the XML result into array
        if ($data === false) {
            $error = curl_error($ch);
            echo $error;
            die('error occured');
        } else {
           // $data =  
            $data = json_decode(json_encode(simplexml_load_string($data)), true);
            echo "<PRE>";
            print_r($data);
            
            // $trackingNumber= $data['PackageTrackingInfo']['TrackingNumber'];
            // $city= $data['PackageTrackingInfo']['PackageDestinationLocation']['City'];
            // $PostalCode= $data['PackageTrackingInfo']['PackageDestinationLocation']['PostalCode'];
            // $CountryCode= $data['PackageTrackingInfo']['PackageDestinationLocation']['CountryCode'];

            // echo $trackingNumber." ".$city." ".$PostalCode." ".$CountryCode;
            
            // foreach($data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail'] as $key1=>$value1)
            // {
            //     foreach($data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail'][$key1] as $key2=>$value2)
            //     {   if(!is_array($value2) && $key2!= 'EventStatus')
            //         {
            //         $newArray[$key1][$ofset]= $value2;
            //         $ofset++;
            //         }
            //     }
            //     $eventCity= $data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail'][$key1]['EventLocation']['City'];
            //     $newArray[$key1][$ofset]= $eventCity;
            //     $ofset= 0;
            // }

            // print_r($newArray);
        }

        curl_close($ch);
    } catch (Exception  $e) {
        echo 'Message: ' . $e->getMessage();
        die("Error");
    }
});
