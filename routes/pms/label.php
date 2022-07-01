<?php

use Illuminate\Support\Facades\Route;

Route::prefix('label/')->group(function () {

    Route::get('manage', 'label\labelManagementController@manage')->name('label.manage');
    Route::get('excel/template', 'label\labelManagementController@downloadExcelTemplate');
    Route::get('upload', 'label\labelManagementController@upload');
    Route::post('upload/excel', 'label\labelManagementController@uploadExcel');
    Route::get('template', 'label\labelManagementController@labelTemplate');
});
Route::get('label/search-label', 'label\labelManagementController@SearchLabel');
Route::post('label/select-label', 'label\labelManagementController@GetLabel');
Route::get('label/pdf-template/{id}', 'label\labelManagementController@showTemplate');
Route::post('label/export-pdf', 'label\labelManagementController@ExportLabel');
Route::get('label/download/{awb_no}', 'label\labelManagementController@downloadLabel');
Route::get('label/download-direct/{id}', 'label\labelManagementController@DownloadDirect');
Route::get('label/print-selected/{id}', 'label\labelManagementController@PrintSelected');
Route::POST('label/select-download', 'label\labelManagementController@DownloadSelected');
Route::get('label/zip-download/{arr}', 'label\labelManagementController@zipDownload');