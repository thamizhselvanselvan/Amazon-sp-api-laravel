<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;



Route::get('BOE/index', 'BOE\BOEController@index');
Route::get('BOE/upload', 'BOE\BOEController@BOEPdfUploadView');
Route::post('BOE/bulk-upload', 'BOE\BOEController@BulkPdfUpload');
Route::get('BOE/pdf-reader', 'BOE\BOEController@BOEPDFReader');
Route::get('BOE/Export', 'BOE\BOEController@BOEExportToCSV');
Route::get('BOE/Export/view', 'BOE\BOEController@BOEExportView');
Route::post('BOE/Export/filter', 'BOE\BOEController@BOEFilterExport')->name('BOE.Export.Filter');
Route::get('BOE/Download', 'BOE\BOEController@Download_BOE');
Route::get('BOE/readfromfile', 'BOE\BOEController@ReadFromfile');
Route::get("BOE/upload/do", 'BOE\BOEController@Upload');

