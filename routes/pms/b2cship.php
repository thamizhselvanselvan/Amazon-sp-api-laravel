<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;


Route::get('B2cship/kyc', 'B2cship\B2cshipKycController@index');
Route::get('B2cship/tracking_status/details', 'B2cship\TrackingStatusController@trackingStatusDetails');
Route::get('B2cship/booking', 'B2cship\B2cshipbookingController@Bookingstatus');


