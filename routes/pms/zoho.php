<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('zoho/dashboard', 'Zoho\ZohoController@Dashboard');
Route::get('zoho/getLeadsDetails/{leadId}', 'Zoho\ZohoController@getOrderDetails');
Route::get('zoho/insertZohoOrder', 'Zoho\ZohoController@addOrderItemsToZoho');
