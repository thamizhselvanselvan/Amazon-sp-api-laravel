<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::prefix('seller/')->group(function () {
    //Asin
    Route::get('asin-master', 'Seller\AsinMasterController@index')->name('asin-master');
    Route::get('add-asin', 'Seller\AsinMasterController@addAsin');
    Route::get('import-bulk-asin', 'Seller\AsinMasterController@importBulkAsin');
    Route::get('export-asin', 'Seller\AsinMasterController@exportAsinToCSV');
    Route::post('add-bulk-asin', 'Seller\AsinMasterController@addBulkAsin');
    Route::get('asinMaster_download', 'Seller\AsinMasterController@download_asin_master')->name('download.asinMaster');
    Route::get('edit-asin/{id}', 'Seller\AsinMasterController@editasin');
    Route::put('edit-save/{id}', 'Seller\AsinMasterController@update')->name('asin.update');
    Route::post('asin/soft-delete/{id}', 'Seller\AsinMasterController@trash');
    Route::get('asin/trash-view', 'Seller\AsinMasterController@trashView')->name('trash.view');
    Route::post('asin/restore/{id}', 'Seller\AsinMasterController@restore')->name('restore.view');
    Route::get('asin/delete', 'Seller\AsinMasterController@deleteAsinView');
    Route::post('asin/remove', 'Seller\AsinMasterController@SellerAsinRemove');
    Route::get('csv/template', 'Seller\AsinMasterController@DownloadCSVTemplate');

    //catalog
    Route::get('catalog-details', 'Seller\SellerCatalogController@ImportCatalogDetails');
    Route::get('catalog', 'Seller\SellerCatalogController@index');
    Route::get('catalog/export', 'Seller\SellerCatalogController@catalogExport');
    Route::get('catalog/download', 'Seller\SellerCatalogController@catalogDownload');

    //pricing

    Route::get('price/details', 'Seller\SellerCatalogController@Pricing');
    Route::get('price/get', 'Seller\SellerCatalogController@GetPrice');
    Route::get('price/export', 'Seller\SellerCatalogController@ExportPricing');
    Route::get('price/download', 'Seller\SellerCatalogController@PriceFileDownload');
    Route::get('price/download/{id}', 'Seller\SellerCatalogController@Download');
    //credentials
    Route::get('credentials', 'Seller\SellerController@index');

    //Invoice
    Route::get('invoice', 'Seller\SellerController@sellerInvoice');
});
