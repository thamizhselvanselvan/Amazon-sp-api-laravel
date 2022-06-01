<?php

use Illuminate\Support\Facades\Route;

Route::get('invoice/manage', 'invoice\InvoiceManagementController@Index');
Route::get('invoice/upload', 'invoice\InvoiceManagementController@Upload');
Route::post('invoice/upload/excel', 'invoice\InvoiceManagementController@UploadExcel');