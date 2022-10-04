<?php

use Illuminate\Support\Facades\Route;

$method = ['get', 'post'];

Route::get('shipntrack/smsa', 'shipntrack\SMSA\SmsaExperessController@index')->name('shipntrack.smsa');
Route::get('shipntrack/smsa/moredetails/{awbno}', 'shipntrack\SMSA\SmsaExperessController@PacketMoreDetails');
Route::get('shipntrack/smsa/upload', 'shipntrack\SMSA\SmsaExperessController@uploadAwb')->name('shipntrack.smsa.upload');
Route::post('shipntrack/smsa/gettracking', 'shipntrack\SMSA\SmsaExperessController@GetTrackingDetails')->name('shipntrack.smsa.gettracking');

Route::get('shipntrack/bombino', 'shipntrack\Bombino\BombinoExpressController@index')->name('shipntrack.bombino');
Route::get('shipntrack/bombino/upload', 'shipntrack\Bombino\BombinoExpressController@upload')->name('shipntrack.bombino.upload');
Route::post('shipntrack/bombino/gettracking', 'shipntrack\Bombino\BombinoExpressController@getTracking')->name('shipntrack.bombino.gettracking');

Route::get('shipntrack/forwarder', 'shipntrack\Forwarder\ForwarderPacketMappingController@index')->name('shipntrack.forwarder');
Route::get('shipntrack/forwarder/template/download', 'shipntrack\Forwarder\ForwarderPacketMappingController@templateDownload')->name('shipntrack.forwarder.template');
Route::get('shipntrack/forwarder/upload', 'shipntrack\Forwarder\ForwarderPacketMappingController@Upload')->name('shipntrack.forwarder.upload');
Route::post('shipntrack/forwarder/save', 'shipntrack\Forwarder\ForwarderPacketMappingController@save')->name('shipntrack.forwarder.save');

Route::get('shipntrack/missing/find', 'shipntrack\Forwarder\ForwarderPacketMappingController@missingexpview')->name('shipntrack.missing.find');
Route::get('shipntrack/missing/export', 'shipntrack\Forwarder\ForwarderPacketMappingController@missexport')->name('shipntrack.missing.export');
Route::get('shipntrack/missing/download', 'shipntrack\Forwarder\ForwarderPacketMappingController@downexp')->name('shipntrack.missing.download');

Route::get('shipntrack/event-master', 'shipntrack\EventMaster\TrackingEventMasterController@index')->name('shipntrack.trackingEvent.back');
Route::post('shipntrack/event-master/insert', 'shipntrack\EventMaster\TrackingEventMasterController@TrackingEventRecordInsert')->name('shipntrack.trackingEvent.save');
Route::get('shipntrack/event-master/upload', 'shipntrack\EventMaster\TrackingEventMasterController@upload')->name('shipntrack.trackingEvent.upload');
Route::POST('shipntrack/event-master/save-file', 'shipntrack\EventMaster\TrackingEventMasterController@TrackingEventFileSave')->name('shipntrack.eventMaster.filesave');
Route::get('shipntrack/event-master/{id}', 'shipntrack\EventMaster\TrackingEventMasterController@EventMasterEdit');
Route::POST('shipntrack/event-master/update/{id}', 'shipntrack\EventMaster\TrackingEventMasterController@EventMasterUpdate')->name('shipntrack.eventMaster.update');
Route::get('shipntrack/event-master/delete/{id}', 'shipntrack\EventMaster\TrackingEventMasterController@EventMasterDelete')->name('shipntrack.eventMaster.delete');


Route::get('/shipntrack/event-mapping', 'shipntrack\EventMapping\TrackingEventMappingController@index')->name('shipntrack.EventMapping.back');
Route::POST('shipntrack/event-mapping/source', 'shipntrack\EventMapping\TrackingEventMappingController@MappingSource');
Route::POST('shipntrack/event-mapping/save', 'shipntrack\EventMapping\TrackingEventMappingController@EventMappingRecordInsert')->name('shipntrack.EventMapping.save');
Route::get('shipntrack/event-mapping/delete/{id}', 'shipntrack\EventMapping\TrackingEventMappingController@EventMappingRecordDelete')->name('shipntrack.EventMapping.delete');

Route::get('shipntrack/event-mapping/edit/{id}', 'shipntrack\EventMapping\TrackingEventMappingController@EventMappingRecordEdit')->name('shipntrack.EventMapping.edit');
Route::POST('shipntrack/event-mapping/update/{id}', 'shipntrack\EventMapping\TrackingEventMappingController@EventMappingRecordUpdate')->name('shipntrack.EventMapping.update');

Route::get('shipntrack/tracking', 'shipntrack\Tracking\TrackingController@Tracking')->name('shipntrack.tracking');
Route::match($method, 'shipntrack/stopTracking', 'shipntrack\Tracking\TrackingController@StopTracking')->name('shipntrack.stop');
Route::post('shipntrack/stopTrackingUpadate', 'shipntrack\Tracking\TrackingController@StopTrackingUpdate')->name('shipntrack.stop.update');

// Route::get('shipntrack/trackingList', 'shipntrack\TrackingList\TrackingListController@index');
// Route::get('shipntrack/trackingList/search', 'shipntrack\TrackingList\TrackingListController@SearchByAwbNo');
