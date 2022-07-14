<?php

use Illuminate\Support\Facades\Route;

Route::get('shipntrack/index', 'shipntrack\ShipntrackManagementController@Index');
Route::get('shipntrack/upload', 'shipntrack\ShipntrackManagementController@upload');
Route::post('shipntrack/upload/csv', 'shipntrack\ShipntrackManagementController@uploadCsv');
Route::get('shipntrack/template/download', 'shipntrack\ShipntrackManagementController@templateDownload');
Route::get('shipntrack/get', 'shipntrack\ShipntrackManagementController@GetDataTable');

Route::get('shipntrack/smsa', 'shipntrack\SMSA\SmsaExperessController@index')->name('shipntrack.smsa');
Route::get('shipntrack/smsa/upload', 'shipntrack\SMSA\SmsaExperessController@uploadAwb')->name('shipntrack.smsa.upload');
Route::post('shipntrack/smsa/gettracking', 'shipntrack\SMSA\SmsaExperessController@GetTrackingDetails')->name('shipntrack.smsa.gettracking');
 
