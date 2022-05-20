<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::prefix('seller/')->group(function () {
    Route::get('asin-master', 'seller\AsinMasterController@index');
    Route::get('add-asin', 'seller\AsinMasterController@addAsin');
    Route::get('import-bulk-asin', 'seller\AsinMasterController@importBulkAsin');
    Route::get('export-asin', 'seller\AsinMasterController@exportAsinToCSV');
    Route::post('add-bulk-asin', 'seller\AsinMasterController@addBulkAsin');
    Route::get('asinMaster_download', 'seller\AsinMasterController@download_asin_master')->name('download.asinMaster');
    Route::get('edit-asin/{id}', 'seller\AsinMasterController@editasin');
    Route::put('edit-save/{id}', 'seller\AsinMasterController@update')->name('asin.update');
    Route::post('asin/soft-delete/{id}', 'seller\AsinMasterController@trash');
    Route::get('asin/trash-view', 'seller\AsinMasterController@trashView')->name('trash.view');
    Route::post('asin/restore/{id}', 'seller\AsinMasterController@restore')->name('restore.view');
});
