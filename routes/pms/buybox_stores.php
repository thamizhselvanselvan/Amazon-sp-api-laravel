<?php

use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Route;
use App\Models\Buybox_stores\Product_Push;
use App\Services\AmazonFeedApiServices\AmazonFeedProcess;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

Route::get('buybox/stores', 'Buybox_stores\BuyBoxStoreController@index')->name('buybox.stores');
Route::post('buybox/latency', 'Buybox_stores\BuyBoxStoreController@latencyupdate')->name('buybox.latency.update');
Route::get('buybox/all/export', 'Buybox_stores\BuyBoxStoreController@exportall')->name('buybox.export.all');
Route::get('buybox/all/export/download', 'Buybox_stores\BuyBoxStoreController@exportdownload')->name('buybox.export.all.download');
Route::get('buybox/all/export/download/local/{index}', 'Buybox_stores\BuyBoxStoreController@DownloadCataloglocal')->name('buybox.download.all.lacal');
Route::get('buybox/sp_api_push', 'Buybox_stores\BuyBoxStoreController@get_price_push')->name('buybox.sp_spi_push_get');
Route::post('stores/listing/price/price_push_update', 'Buybox_stores\BuyBoxStoreController@push_price_update')->name('buybox.store.push_price_update');
Route::post('stores/listing/price/store_data_export', 'Buybox_stores\BuyBoxStoreController@store_data_export')->name('buybox.store.store_data_export');
Route::get('stores/listing/price', 'Buybox_stores\BuyBoxStoreController@storeslisting')->name('buybox.store.listing');
Route::get('stores/listing/price/{store_id}', 'Buybox_stores\BuyBoxStoreController@storeslisting')->name('buybox.store.listing.storewise');
Route::get('stores/listing/availability', 'Buybox_stores\BuyBoxStoreController@availability')->name('buybox.store.availability');
Route::get('stores/listing/availability/{store_id}', 'Buybox_stores\BuyBoxStoreController@availability')->name('buybox.store.availability.storewise');
Route::post('stores/price/push/availability', 'Buybox_stores\BuyBoxStoreController@PricePushAvailability')->name('buybox.store.price.push.availability');
Route::get('stores/listing/price/update/{id}', 'Buybox_stores\BuyBoxStoreController@updateprice')->name('buybox.store.listing.storewise.update');
Route::get('stores/price/updated', 'Buybox_stores\BuyBoxStoreController@updatepricelisting')->name('buybox.store.listing.updated');

Route::get('stores/price/file/get', 'Buybox_stores\BuyBoxStoreController@fileget')->name('buybox.store.file.get');
Route::get('stores/price/file/download/{index}', 'Buybox_stores\BuyBoxStoreController@filedownload')->name('buybox.store.file.download');

Route::get('stores/region/fetch', 'Buybox_stores\BuyBoxStoreController@requestregion')->name('buybox.region.fetch');

Route::get("amazo", function () {

    $id = 0;
    $store_id = 10;
    $seller_id = 'A2BWJVKSWP7TR2';
    $country_code = 'IN';
    $sku = 'DL-B001E63NE4';
    $marketplace_ids = 'A21TJRUUN4KGV';

    $feedLists[] = [
        "push_price" => '3801',
        "product_sku" => "DL-B001E63NE4",
        "base_price" => "3500",
    ];

    //$feedSubmit = (new AmazonFeedProcess)->feedSubmit($feedLists, $store_id, $id, false);
    // $response = json_decode(json_encode($feedSubmit));
    //dd($feedSubmit);
    //po($response);

    //$feedback_id = $feedSubmit;
    $feedback_id = 50928019395;
    $feedback_id = 50929019395;

    // $url  = (new FeedOrderDetailsApp360())->getLists($feedLists, $store_id, $country_code);
    // $url  = (new FeedOrderDetailsApp360())->get($feedback_id, $store_id, $country_code);
    $url  = (new FeedOrderDetailsApp360())->getLists($feedback_id, $store_id, $country_code);

    dd($url);
});
