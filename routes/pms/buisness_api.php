<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;


Route::get('product/details', 'BuisnessAPI\SearchProductRequestController@searchproductRequest');
Route::resource('buisness/search/products', 'BuisnessAPI\SearchProductRequestController');


Route::get('buisness/product/details', 'BuisnessAPI\ProductsRequestController@productRequestasin');
Route::resource('buisness/products', 'BuisnessAPI\ProductsRequestController');
