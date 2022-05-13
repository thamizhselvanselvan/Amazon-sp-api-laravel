<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('b2cship/kyc', 'B2cship\B2cshipKycController@index');
Route::get('b2cship/tracking_status/details', 'B2cship\TrackingStatusController@trackingStatusDetails');
Route::get('b2cship/booking', 'B2cship\B2cshipbookingController@Bookingstatus');
Route::get('b2cship/micro_status_report', 'B2cship\TrackingStatusController@microStatusReport');
Route::get('b2cship/micro_status_missing_report', 'B2cship\TrackingStatusController@microStatusMissingReport');
Route::get('b2cship/update-report', 'B2cship\TrackingStatusController@update_report');

Route::get('bombion/packet-activities','B2cship\BombinoPacketActivitiesController@PacketActivitiesDetails');
Route::get('bombion/update-packet-details','B2cship\BombinoPacketActivitiesController@UpdatePacketDetails');



