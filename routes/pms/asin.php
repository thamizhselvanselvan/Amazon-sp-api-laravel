<?php
use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;


Route::get('asin-master', 'AsinMasterController@index')->name('asin-master');
Route::get('add-asin', 'AsinMasterController@addAsin');
Route::get('import-bulk-asin', 'AsinMasterController@importBulkAsin');
Route::get('export-asin', 'AsinMasterController@exportAsinToCSV');
Route::post('add-bulk-asin', 'AsinMasterController@addBulkAsin');
Route::get('asinMaster_download', 'AsinMasterController@download_asin_master')->name('download.asinMaster');
Route::get('edit-asin/{id}', 'AsinMasterController@editasin');
Route::put('edit-save/{id}','AsinMasterController@update')->name('asin.update');
Route::post('asin/soft-delete/{id}', 'AsinMasterController@trash');
Route::get('asin/trash-view', 'AsinMasterController@trashView')->name('trash.view');
Route::post('asin/restore/{id}', 'AsinMasterController@restore')->name('restore.view');

