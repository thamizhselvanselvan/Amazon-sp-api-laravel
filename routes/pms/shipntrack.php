<?php

use Illuminate\Support\Facades\Route;

Route::get('shipntrack/manage', 'Shipntrack\ShipntrackManagementController@index');
Route::post('shipntrack/upload/csv', 'Shipntrack\ShipntrackManagementController@uploadCsv');