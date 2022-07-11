<?php

use Illuminate\Support\Facades\Route;

Route::get('shipntrack/manage', 'shipntrack\ShipntrackManagementController@Index');
Route::post('shipntrack/upload/csv', 'shipntrack\ShipntrackManagementController@uploadCsv');
