<?php

use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Route;
use App\Models\Buybox_stores\Product_Push;

Route::get('buybox/stores', 'Buybox_stores\BuyBoxStoreController@index')->name('buybox.stores');
Route::post('buybox/latency', 'Buybox_stores\BuyBoxStoreController@latencyupdate')->name('buybox.latency.update');
Route::get('buybox/all/export', 'Buybox_stores\BuyBoxStoreController@exportall')->name('buybox.export.all');
Route::get('buybox/all/export/download', 'Buybox_stores\BuyBoxStoreController@exportdownload')->name('buybox.export.all.download');
Route::get('buybox/all/export/download/local/{index}', 'Buybox_stores\BuyBoxStoreController@DownloadCataloglocal')->name('buybox.download.all.lacal');


Route::get('buybox/sp_api_push', 'Buybox_stores\BuyBoxStoreController@get_price_push')->name('buybox.sp_spi_push_get');



Route::post('stores/listing/price/updated', 'Buybox_stores\BuyBoxStoreController@storespriceupdated')->name('buybox.store.updated');
Route::get('stores/listing/price', 'Buybox_stores\BuyBoxStoreController@storeslisting')->name('buybox.store.listing');
Route::get('stores/listing/price/{store_id}', 'Buybox_stores\BuyBoxStoreController@storeslisting')->name('buybox.store.listing.storewise');
Route::get('stores/listing/price/update/{id}', 'Buybox_stores\BuyBoxStoreController@updateprice')->name('buybox.store.listing.storewise.update');

