<?php

use Illuminate\Support\Facades\Route;

Route::prefix('label/')->group(function () {

    Route::get('manage', 'label\labelManagementController@manage')->name('label.manage');
    Route::get('excel/template', 'label\labelManagementController@downloadExcelTemplate');
    Route::get('upload', 'label\labelManagementController@upload');
    Route::post('upload/excel', 'label\labelManagementController@uploadExcel');
    Route::get('template', 'label\labelManagementController@labelTemplate');
    Route::get('missing', 'label\labelManagementController@missing');
    Route::post('missing/order_id', 'label\labelManagementController@missingOrderId');
});

Route::match(['get', 'post'], 'label/search-label', 'label\labelManagementController@SearchLabel')->name('label.search-label');
// Route::post('label/select-label', 'label\labelManagementController@GetLabel');
Route::get('label/pdf-template/{id}', 'label\labelManagementController@showTemplate');
Route::post('label/export-pdf', 'label\labelManagementController@ExportLabel');
Route::get('label/download/{bag_no}/{awb_no}', 'label\labelManagementController@downloadLabel');
Route::get('label/download-direct/{id}', 'label\labelManagementController@DownloadDirect');
Route::get('label/print-selected/{id}', 'label\labelManagementController@PrintSelected');
Route::POST('label/select-download', 'label\labelManagementController@DownloadSelected');
// Route::get('label/zip-download/{arr}', 'label\labelManagementController@zipDownload');
Route::post('label/zip/download', 'label\labelManagementController@zipDownload')->name('label.zip.download');
Route::get('label/zip/download/{bag_no}/zip/{file_name}', 'label\labelManagementController@zipDownloadLink');

Route::get('label/missing/address', 'label\labelManagementController@labelMissingAddress');
Route::post('label/missing/address', 'label\labelManagementController@labelMissingAddressUpload');
Route::get('label/missing/address/export', 'label\labelManagementController@labelMissingAddressExport')->name('label.missing.address.export');

Route::match(['get', 'post'], 'label/search/amazon-order-id', 'label\labelManagementController@labelSearchByOrderId')->name('lable.search.amazon-order-id');
Route::match(['get', 'post'], 'label/search/awb-tracking-id', 'label\labelManagementController@labelSearchByAwnNo')->name('lable.search.by.awb.no');
Route::match(['get', 'post'], 'label/update/tracking-details', 'label\labelManagementController@updateTrackingDetails')->name('lable.update.tracking-details');

Route::get('label/edit-order-address/{id}', 'label\labelManagementController@editOrderAddress');
Route::put('label/update-order-address/{id}', 'label\labelManagementController@updateOrderAddress');

Route::get('label/edit-order-address-search-id/{id}', 'label\labelManagementController@editordersearchbyid');
Route::put('label/update-order-address-search-id/{id}', 'label\labelManagementController@updateordersearchbyid');

Route::get('label/file/management/monitor', 'label\labelManagementController@LabelFileManagementMonitor')->name('label.file.management.monitor');
