<?php

use Illuminate\Support\Facades\Route;

Route::get('shipntrack/smsa', 'shipntrack\SMSA\SmsaExperessController@index')->name('shipntrack.smsa');
Route::get('shipntrack/smsa/moredetails/{awbno}', 'shipntrack\SMSA\SmsaExperessController@PacketMoreDetails');
Route::get('shipntrack/smsa/upload', 'shipntrack\SMSA\SmsaExperessController@uploadAwb')->name('shipntrack.smsa.upload');
Route::post('shipntrack/smsa/gettracking', 'shipntrack\SMSA\SmsaExperessController@GetTrackingDetails')->name('shipntrack.smsa.gettracking');

Route::get('shipntrack/bombino', 'shipntrack\Bombino\BombinoExpressController@index')->name('shipntrack.bombino');
Route::get('shipntrack/bombino/upload', 'shipntrack\Bombino\BombinoExpressController@upload')->name('shipntrack.bombino.upload');
Route::post('shipntrack/bombino/gettracking', 'shipntrack\Bombino\BombinoExpressController@getTracking')->name('shipntrack.bombino.gettracking');
