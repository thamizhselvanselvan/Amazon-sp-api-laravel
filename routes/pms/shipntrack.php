<?php

use Illuminate\Support\Facades\Route;

Route::get('shipntrack/index', 'shipntrack\ShipntrackManagementController@Index');
Route::get('shipntrack/upload', 'shipntrack\ShipntrackManagementController@upload');
Route::post('shipntrack/upload/csv', 'shipntrack\ShipntrackManagementController@uploadCsv');
Route::get('shipntrack/template/download', 'shipntrack\ShipntrackManagementController@templateDownload');
