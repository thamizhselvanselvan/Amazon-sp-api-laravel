<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\AWS_Business_API\AWS_POC\Orders;
use App\Http\Controllers\PMSPHPUnitTestController;
use App\Http\Controllers\BuisnessAPI\ProductsRequestController;
use App\Services\AWS_Business_API\Details_dump\product_details;

Route::get('product/details', 'BuisnessAPI\SearchProductRequestController@searchproductRequest');
Route::resource('business/search/products', 'BuisnessAPI\SearchProductRequestController');


Route::get('buisness/product/details', 'BuisnessAPI\ProductsRequestController@productRequestasin');
Route::resource('business/products/request', 'BuisnessAPI\ProductsRequestController');

Route::get('buisness/product/offers', 'BuisnessAPI\searchOffersRequestController@searchoffersproduct');
Route::resource('business/offers', 'BuisnessAPI\searchOffersRequestController');

Route::get('business/asin/details', 'BuisnessAPI\GetProductsByAsinsController@searchasinproduct');
Route::resource('business/byasins', 'BuisnessAPI\GetProductsByAsinsController');


Route::resource('business/details', 'BuisnessAPI\ProductDetailsController');
Route::get('buisness/details', 'BuisnessAPI\ProductDetailsController@viewpro');

Route::get('business/orders/details', 'BuisnessAPI\OrdersController@test');
Route::get('business/orders/view', 'BuisnessAPI\OrdersController@getorders');
Route::get('business/orders/pending', 'BuisnessAPI\OrdersController@orderspending');
Route::get('business/offers_view', 'BuisnessAPI\OrdersController@prodoffers');
Route::get('business/order/book', 'BuisnessAPI\OrdersController@orderbooking');
Route::resource('business/orders', 'BuisnessAPI\OrdersController');

// Route::get('product/test', function()
// {
//      $asin = 'B0000531II';
// $tes = new product_details;
//      $tes->savedetails($asin);
//      return 'ok';
// });

// Route::get('product/imp', function () {
//      $tes = new ProductsRequestController;
//      $tes->fetchusasin();

// });



// Route::get('aimeos/imp', function () {

//     $data = DB::connection('cliqnshop')->table('order')

//         ->join('order_base_product as oid', function ($query) {
//             $query->on('oid.baseid', '=', 'order.baseid');
//         })
//         ->join('product as pid', function ($query) {
//             $query->on('pid.id', '=', 'oid.prodid');
//         })
//         ->select('code', 'label')
//         ->get();

//         $call = new Orders;
//       foreach ($data as $val) 
//     {
//         // po($data);
//         $asin = ($val->code);
//         $item_name  = ($val->label);


//         $data =  $call->getOrders($asin, $item_name);
//         $resultxml = $data[2];
//         Storage::disk('local')->put('xml.txt', $resultxml);



//         $responce = ($data[0]);

//         $parse = simplexml_load_string($responce);
//         $xmlr =  json_decode(json_encode($parse), true);
//         $details = ($data[1]);
//         $responce_code = ($xmlr["Response"]["Status"]["@attributes"]["code"]);
//         $responce_text = ($xmlr["Response"]["Status"]["@attributes"]["text"]);
//         $receved_payload = ($xmlr["@attributes"]["payloadID"]);

//         $xml = ($data[2]);

//         $order_details_array = ($data[1]);
//         $order_details = ($order_details_array[0]);
//         $sent_payload = ($order_details["payload"]);
//         $order_date = ($order_details["order_date"]);
//         $org_name =  ($order_details["organization_name"]);
//         $name =  ($order_details["name"]);
//         $email = ($order_details["e_mail"]);
//         $countrycode = ($order_details["country_code"]);
//         $country_name = ($order_details["country_name"]);
//         $order_id = ($order_details["order_id"]);

//         $deliver1 =   ($order_details["delivery_1"]);
//         $deliver2 =   ($order_details["delivery_2"]);
//         $deliver3 =   ($order_details["delivery_3"]);
//         $street = ($order_details["street"]);
//         $city = ($order_details["city"]);
//         $state = ($order_details["state"]);
//         $post_code = ($order_details["post_code"]);
//         $area_code = ($order_details["area_code"]);
//         $phone_no  = ($order_details["phone_no"]);
//         $fax_name  = ($order_details["fax_name"]);

//         $asin = ($order_details["asin"]);
//         $item_description = ($order_details["item_description"]);
//         $unit = ($order_details["unit"]);
//         $class = ($order_details["class"]);
//         $quantity = ($order_details["quantity"]);
//         $ManufacturerName = ($order_details["ManufacturerName"]);
//         $line = ($order_details["line"]);
//         $ManufacturerPartID = ($order_details["ManufacturerPartID"]);
//         $category = ($order_details["category"]);
//         $sub_category = ($order_details["sub_category"]);

//         $item_details = [
//             $asin,
//             $item_description,
//             $unit,
//             $class,
//             $quantity,
//             $ManufacturerName,
//             $line,
//             $ManufacturerPartID,
//             $category,
//             $sub_category,

//         ];

//         $ship_address_array = [
//             $deliver1,
//             $deliver2,
//             $deliver3,
//             $street,
//             $city,
//             $state,
//             $post_code,
//             $area_code,
//             $phone_no,
//             $fax_name,
//         ];

//         DB::connection('business')->table('orders')->insert([
//             // 'xml_sent' => json_encode($xml),
//             'xml_sent' => '',
//             'sent_payload' => $sent_payload,
//             'organization_name' => $org_name,
//             'order_date' => $order_date,
//             'name' => $name,
//             'e-mail' => $email,
//             'country_name' => $country_name,
//             'country_code' => $countrycode,
//             'order_id' => $order_id,
//             'item_details' => json_encode($item_details),
//             'ship_address' => json_encode($ship_address_array),
//             'bill_address' => json_encode($ship_address_array),
//             'responce_payload' => $receved_payload,
//             'responce_text' =>  $responce_text,
//             'responce_code' => $responce_code,
//             'created_at' => now(),
//             'updated_at' => now()
//         ]);
//         // dd($responce, $details);
//     }
    
// });
  

