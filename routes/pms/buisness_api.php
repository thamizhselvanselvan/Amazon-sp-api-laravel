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

Route::resource('business/orders', 'BuisnessAPI\OrdersController');

Route::resource('business/details', 'BuisnessAPI\ProductDetailsController');
Route::get('buisness/details', 'BuisnessAPI\ProductDetailsController@viewpro');


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


  

