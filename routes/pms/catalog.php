<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;


Route::resource('textiles', 'textilesController');
Route::post('import-csv', 'textilesController@importTextiles')->name('import.csv');
Route::get('export_to_csv', 'textilesController@exportTextilesToCSV')->name('export.csv');
Route::get('universalTextiles_download', 'textilesController@download_universalTextiles')->name('download.universalTextiles');

Route::get('file_downloads', 'filedownloads\FileDownloadsController@filedownloads')->name('file.downloads');


Route::get('product/amazon_com', 'product\productController@index')->name('product.amazon_com');
Route::get('product/fetch_from_amazon', 'product\productController@fetchFromAmazon')->name('product.fetch.amazon');
Route::get('product/getPricing', 'product\productController@amazonGetPricing')->name('amazon.getPricing');

Route::get('other-product/amazon_com', 'otherProduct\anotherAmazonProductController@index')->name('product.amazon_com');
Route::post('other-product/export', 'otherProduct\anotherAmazonProductController@exportOtherProduct')->name('export.other-product');
Route::get('other_file_download', 'otherProduct\anotherAmazonProductController@other_file_download')->name('file.other_file_download');
Route::get('other-product/download/{id}', 'otherProduct\anotherAmazonProductController@download_other_product')->name('download.other-product');

Route::get('other-product/amazon_in', 'otherProduct\OtherAmazonInProductController@index')->name('product.amazon_in');
Route::post('other-product/export_in', 'otherProduct\OtherAmazonInProductController@exportOtherProductIn')->name('export.other-product-in');
Route::get('other-prouduct/download_in', 'otherProduct\OtherAmazonInProductController@other_file_download_in');
Route::get('other-product/file_download_in/{id}', 'otherProduct\OtherAmazonInProductController@download_other_product')->name('download.other-product-in');
