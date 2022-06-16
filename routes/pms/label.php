<?php

use Illuminate\Support\Facades\Route;

Route::prefix('label/')->group(function () {

    Route::get('manage', 'label\labelManagementController@manage');
    Route::get('excel/template', 'label\labelManagementController@downloadExcelTemplate');
    Route::get('upload', 'label\labelManagementController@upload');
    Route::post('upload/excel', 'label\labelManagementController@uploadExcel');
    Route::get('template', 'label\labelManagementController@labelTemplate');
    
});
