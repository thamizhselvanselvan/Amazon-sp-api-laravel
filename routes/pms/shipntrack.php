<?php

use Illuminate\Support\Facades\Route;

Route::get('shipntrack/index', 'shipntrack\ShipntrackManagementController@Index');
Route::get('shipntrack/upload', 'shipntrack\ShipntrackManagementController@upload');
Route::post('shipntrack/upload/csv', 'shipntrack\ShipntrackManagementController@uploadCsv');
Route::get('shipntrack/template/download', 'shipntrack\ShipntrackManagementController@templateDownload');

Route::get('shipntrack/smsa/gettracking', 'shipntrack\SMSA\SmsaExperessController@SmsaGetTrackingDetails')->name('shipntrack.smsa.gettracking');
