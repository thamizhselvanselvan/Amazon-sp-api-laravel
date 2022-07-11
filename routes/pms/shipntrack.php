<?php

use Illuminate\Support\Facades\Route;

Route::get('shipntrack/manage', 'shipntrack\ShipntrackManagementController@Index');
Route::get('shipntrack/upload', 'shipntrack\ShipntrackManagementController@manage');
Route::post('shipntrack/upload/csv', 'shipntrack\ShipntrackManagementController@uploadCsv');
Route::get('shipntrack/template/download', 'shipntrack\ShipntrackManagementController@templateDownload');
