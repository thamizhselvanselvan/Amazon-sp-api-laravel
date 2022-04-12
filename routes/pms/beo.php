<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;



Route::get('BOE/index', 'BOE\BOEController@index');
Route::get('BOE/uplod', 'BOE\BOEController@BOEPdfUploadView');
Route::post('BOE/bulk-upload', 'BOE\BOEController@BulkPdfUpload');
Route::get('BOE/pdf-reader', 'BOE\BOEController@BOEPDFReader');
Route::get('BOE/Export', 'BOE\BOEController@BOEExportToCSV');
Route::get('BOE/Download', 'BOE\BOEController@Download_BOE');

