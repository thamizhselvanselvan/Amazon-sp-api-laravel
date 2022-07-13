<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('b2cship/dashboard', 'B2cship\B2cshipDashboardController@Dashboard');
Route::get('b2cship/kyc', 'B2cship\B2cshipKycController@index');
Route::get('b2cship/tracking_status/details', 'B2cship\TrackingStatusController@trackingStatusDetails');
Route::get('b2cship/tracking_status/csv_export', 'B2cship\TrackingStatusController@trackingStatusDetailsExportToCSV');
Route::get('b2cship/booking', 'B2cship\B2cshipbookingController@Bookingstatus');
Route::get('b2cship/micro_status_report', 'B2cship\TrackingStatusController@microStatusReport');
Route::get('b2cship/micro_status_missing_report', 'B2cship\TrackingStatusController@microStatusMissingReport');
Route::get('b2cship/update-report', 'B2cship\TrackingStatusController@update_report');

Route::get('b2cship/monitor', 'B2cship\B2cshipMonitorController@index');

Route::get('bombion/packet-activities','B2cship\BombinoPacketActivitiesController@PacketActivitiesDetails');
Route::get('bombion/update-packet-details','B2cship\BombinoPacketActivitiesController@UpdatePacketDetails');
Route::get('bombion/csv-export','B2cship\BombinoPacketActivitiesController@ExportToCSV');


// Route::get('b2cship/Trackingtab','B2cship\B2cshipDashboardController@showDashboard');
// Route::get('b2cship/TrackingAPInActive','B2cship\B2cshipDashboardController@TrackingApiDetailsInactive');
// Route::get('b2cship/Bombinotab','B2cship\B2cshipDashboardController@BombinoStatus');
// Route::get('b2cship/Deliverytab','B2cship\B2cshipDashboardController@BlueDartAndDeliveryStatus');
// Route::get('b2cship/DeliveryStatusInactive','B2cship\B2cshipDashboardController@DeliveryStatusInactive');
// Route::get('b2cship/Misctab','B2cship\B2cshipDashboardController@BookingAndKycStatusDetails');
// Route::get('b2cship/BombinoStatusInactive','B2cship\B2cshipDashboardController@BombinoInactive');



