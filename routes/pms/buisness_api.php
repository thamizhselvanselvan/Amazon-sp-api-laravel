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



// Route::get('orders', function () {
//     $asin  = ['B00A2JBMRE', 'B06WW6W4KW'];
//     $name = ['Homedics SoundSleep White Noise Sound Machine', 'Homedics SoundSleep Recharged Alarm Clock'];
//     $OfferID = ['HRZ3O4fiD2OI5oG5u9p242Sa0KJyixLtlPmy2xvFPsvB9flV0Flk8KWlDgqRCK6xY1MWoKokKRJFBhB%2BgALwA2x%2BZmKBf4ZOPiWbKoY5HYTIB%2FW0PEsqK%2FqJPZLQcWI%2BNRpjlYvI396cR9XNEr51Y6H5nZHqYvtH', 'OBuz6XT%2Fv5%2BGCXdqq3xUDzqEVkVuF0FkXkMVc0kWWRTCQgHOsFX%2BY%2FLe%2FOQXc8tPSP2Svq4y6JTWMqOHiSySvzzEdDQtLmJXp65IY%2FnFMzEjWrY3iwXlREw6F%2Bk%2FH1d0PD7udz%2FKMOGhY12CXVan6qiHFus143ZhqViBBOO9fz5FP5J1n6PZqkfzZL%2Fm%2Byg6'];
//     $ApiCall = new Orders();

//     $sent[] = ['asin' => $asin , 'item_name' => $name , 'OfferID' => $OfferID];

//     // dd($sent['asin']);
//     // exit;
//     $data = $ApiCall->getOrders($sent);
//     dd($data);
// });
