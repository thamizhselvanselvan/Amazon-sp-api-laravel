<?php

use Illuminate\Support\Facades\Route;

$method = ['get', 'post'];

Route::get('shipntrack/courier/tracking', 'shipntrack\Tracking\CourierTrackingController@index')->name('shipntrack.courier.tracking');
Route::get('shipntrack/courier/moredetails/{sourceDestination}/{awbno}', 'shipntrack\Tracking\CourierTrackingController@PacketMoreDetails');
Route::get('shipntrack/courier/track', 'shipntrack\Tracking\CourierTrackingController@getDetails')->name('shipntrack.courier.track');

// Route::get('shipntrack/smsa/upload', 'shipntrack\Tracking\CourierTrackingController@uploadAwb')->name('shipntrack.smsa.upload');
// Route::post('shipntrack/smsa/gettracking', 'shipntrack\Tracking\CourierTrackingController@GetTrackingDetails')->name('shipntrack.smsa.gettracking');

Route::get('shipntrack/bombino', 'shipntrack\Bombino\BombinoExpressController@index')->name('shipntrack.bombino');
Route::get('shipntrack/bombino/upload', 'shipntrack\Bombino\BombinoExpressController@upload')->name('shipntrack.bombino.upload');
Route::post('shipntrack/bombino/gettracking', 'shipntrack\Bombino\BombinoExpressController@getTracking')->name('shipntrack.bombino.gettracking');

/* Forwarder Mapping */
Route::match($method, 'shipntrack/forwarder', 'shipntrack\Forwarder\ForwarderPacketMappingController@index')->name('shipntrack.forwarder');
Route::get('shipntrack/forwarder/template/download', 'shipntrack\Forwarder\ForwarderPacketMappingController@templateDownload')->name('shipntrack.forwarder.template');
Route::get('shipntrack/forwarder/upload', 'shipntrack\Forwarder\ForwarderPacketMappingController@Upload')->name('shipntrack.forwarder.upload');
Route::post('shipntrack/forwarder/save', 'shipntrack\Forwarder\ForwarderPacketMappingController@save')->name('shipntrack.forwarder.save');
Route::match($method, 'shipntrack/forwarder/search', 'shipntrack\Forwarder\ForwarderPacketMappingController@singlesearch')->name('shipntrack.forwarder.search');
Route::post('shipntrack/forwarder/update', 'shipntrack\Forwarder\ForwarderPacketMappingController@forwarderupdate')->name('shipntrack.forwarder.update');
//new 
Route::post('shipntrack/forwarder/store/forwarder', 'shipntrack\Forwarder\ForwarderPacketMappingController@store_farwarder')->name('shipntrack.forwarder.store.forwarder');
Route::get('shipntrack/forwarder/select/view', 'shipntrack\Forwarder\ForwarderPacketMappingController@courierget')->name('shipntrack.forwarder.select.view');
Route::get('shipntrack/forwarder/mapped/details', 'shipntrack\Forwarder\ForwarderPacketMappingController@listing')->name('shipntrack.forwarder.mapped.details');
Route::get('shipntrack/missing/find', 'shipntrack\Forwarder\ForwarderPacketMappingController@missingexpview')->name('shipntrack.missing.find');
Route::get('shipntrack/missing/export', 'shipntrack\Forwarder\ForwarderPacketMappingController@missexport')->name('shipntrack.missing.export');
Route::get('shipntrack/missing/download', 'shipntrack\Forwarder\ForwarderPacketMappingController@downexp')->name('shipntrack.missing.download');
Route::get('shipntrack/shipment/edit/{destination}', 'shipntrack\Forwarder\ForwarderPacketMappingController@editshipment')->name('shipntrack.edit.shipment');
Route::get('shipntrack/shipment/data/edit', 'shipntrack\Forwarder\ForwarderPacketMappingController@editdata')->name('shipntrack.forwarder.edit.view');
Route::post('shipntrack/shipment/save/edit', 'shipntrack\Forwarder\ForwarderPacketMappingController@edit_store')->name('shipntrack.forwarder.save.edit');

/* Booking Listing */
Route::get('shipntrack/booking/details', 'shipntrack\Forwarder\ForwarderPacketMappingController@booking_list')->name('shipntrack.booking.list');

/* Tracking Event master */
Route::get('shipntrack/event-master', 'shipntrack\EventMaster\TrackingEventMasterController@index')->name('shipntrack.trackingEvent.back');
Route::post('shipntrack/event-master/insert', 'shipntrack\EventMaster\TrackingEventMasterController@TrackingEventRecordInsert')->name('shipntrack.trackingEvent.save');
Route::get('shipntrack/event-master/upload', 'shipntrack\EventMaster\TrackingEventMasterController@upload')->name('shipntrack.trackingEvent.upload');
Route::POST('shipntrack/event-master/save-file', 'shipntrack\EventMaster\TrackingEventMasterController@TrackingEventFileSave')->name('shipntrack.eventMaster.filesave');
Route::get('shipntrack/event-master/{id}', 'shipntrack\EventMaster\TrackingEventMasterController@EventMasterEdit');
Route::POST('shipntrack/event-master/update/{id}', 'shipntrack\EventMaster\TrackingEventMasterController@EventMasterUpdate')->name('shipntrack.eventMaster.update');
Route::get('shipntrack/event-master/delete/{id}', 'shipntrack\EventMaster\TrackingEventMasterController@EventMasterDelete')->name('shipntrack.eventMaster.delete');

/* Event Mapping  */
Route::get('/shipntrack/event-mapping', 'shipntrack\EventMapping\TrackingEventMappingController@index')->name('shipntrack.EventMapping.back');
Route::POST('shipntrack/event-mapping/source', 'shipntrack\EventMapping\TrackingEventMappingController@MappingSource');
Route::POST('shipntrack/event-mapping/save', 'shipntrack\EventMapping\TrackingEventMappingController@EventMappingRecordInsert')->name('shipntrack.EventMapping.save');
Route::get('shipntrack/event-mapping/delete/{id}', 'shipntrack\EventMapping\TrackingEventMappingController@EventMappingRecordDelete')->name('shipntrack.EventMapping.delete');
Route::get('shipntrack/event-mapping/edit/{id}', 'shipntrack\EventMapping\TrackingEventMappingController@EventMappingRecordEdit')->name('shipntrack.EventMapping.edit');
Route::POST('shipntrack/event-mapping/update/{id}', 'shipntrack\EventMapping\TrackingEventMappingController@EventMappingRecordUpdate')->name('shipntrack.EventMapping.update');

/* Stop Tracking Management */
Route::get('shipntrack/tracking', 'shipntrack\Tracking\TrackingController@Tracking')->name('shipntrack.tracking');
Route::match($method, 'shipntrack/stopTracking', 'shipntrack\Tracking\TrackingController@StopTracking')->name('shipntrack.stop');
Route::post('shipntrack/stopTrackingUpadate', 'shipntrack\Tracking\TrackingController@StopTrackingUpdate')->name('shipntrack.stop.update');

/* Courier Crud */
Route::match($method, 'shipntrack/courier', 'shipntrack\Courier\CourierController@index')->name('shipntrack.courier.index');
Route::post('shipntrack/courier/store', 'shipntrack\Courier\CourierController@couriergsave')->name('snt.courier.store');
Route::get('shipntrack/courier/{id}/edit', 'shipntrack\Courier\CourierController@courieredit')->name('snt.courier.edit');
Route::post('shipntrack/courier/save/edit', 'shipntrack\Courier\CourierController@courierupdate')->name('snt.courier.update');
Route::get('shipntrack/courier/{id}/remove', 'shipntrack\Courier\CourierController@courierremove')->name('snt.courier.remove');

/* Courier Partner Management */
Route::match($method, 'shipntrack/partners', 'shipntrack\Courier\CourierPartnerController@index')->name('snt.courier.index');
Route::match($method, 'shipntrack/partners/create', 'shipntrack\Courier\CourierPartnerController@create')->name('courier.partners.create');
Route::match($method, 'shipntrack/partners/store', 'shipntrack\Courier\CourierPartnerController@store')->name('courier.partners.store');
Route::match($method, 'shipntrack/partners/remove/{id}', 'shipntrack\Courier\CourierPartnerController@destroy')->name('courier.partners.delete');
Route::match($method, 'shipntrack/partner/{id}/edit', 'shipntrack\Courier\CourierPartnerController@edit')->name('courier.partners.edit');
Route::match($method, 'shipntrack/partners/partner/update/{id}', 'shipntrack\Courier\CourierPartnerController@update')->name('courier.partners.update');

/* Internal Status Management */
Route::get('shipntrack/booking', 'shipntrack\BookingMasterController@index')->name('snt.booking.index');
Route::post('shipntrack/booking/store', 'shipntrack\BookingMasterController@bookingsave')->name('snt.booking.store');
Route::get('shipntrack/booking/{id}/edit', 'shipntrack\BookingMasterController@bookingedit')->name('snt.booking.edit');
Route::post('shipntrack/booking/save/edit', 'shipntrack\BookingMasterController@bookingformedit')->name('snt.booking.update');
Route::get('shipntrack/booking/{id}/remove', 'shipntrack\BookingMasterController@bookingremove')->name('snt.booking.remove');

/*Courier Status Management */
Route::get('shipntrack/status/manager', 'shipntrack\Courier\CourierStatusManagementController@index')->name('status.master.index');
Route::get('shipntrack/status/manager/{courier_id}', 'shipntrack\Courier\CourierStatusManagementController@index')->name('status.master.courier_id');
Route::get('shipntrack/status/store', 'shipntrack\Courier\CourierStatusManagementController@storestatus')->name('shipntrack.courier.status.store');

/* SNT Process Master */
Route::get('shipntrack/process/home', 'shipntrack\ProcessManagement\ProcessManagementController@index')->name('snt.process.home');
Route::post('shipntrack/process/store', 'shipntrack\ProcessManagement\ProcessManagementController@store')->name('snt.process.store');
Route::get('shipntrack/process/{id}/edit', 'shipntrack\ProcessManagement\ProcessManagementController@update_view')->name('snt.process.update.view');
Route::post('shipntrack/process/update', 'shipntrack\ProcessManagement\ProcessManagementController@update')->name('snt.process.update');
Route::get('shipntrack/process/{id}/remove', 'shipntrack\ProcessManagement\ProcessManagementController@remove')->name('snt.process.remove');


// POD
Route::get('shipntrack/b2c/POD', 'shipntrack\POD\B2cProofOfDeliveryController@index')->name('shipntrack_POD');
Route::get('shipntrack/b2c/templete', 'shipntrack\POD\B2cProofOfDeliveryController@templete')->name('shipntrack_templete');

//SNT Invoice
Route::get('shipntrack/invoice/index', 'shipntrack\Invoice\ShipnTrackInvoiceManagementController@index')->name('shipntrack.invoice.home');
Route::get('shipntrack/invoice/template/{destination}/{id}', 'shipntrack\Invoice\ShipnTrackInvoiceManagementController@SNTInvoiceTemplate')->name('shipntrack.invoice.template');


//SNT Label Master
Route::get('shipntrack/label/master', 'shipntrack\Operations\Label\ShipnTrackLabelMasterController@index')->name('shipntrack.label.master.index');
Route::post('shipntrack/label/master/submit', 'shipntrack\Operations\Label\ShipnTrackLabelMasterController@LabelMasterFormSubmit')->name('shipntrack.label.master.submit');
Route::post('shipntrack/label/master/edit', 'shipntrack\Operations\Label\ShipnTrackLabelMasterController@LabelMasterFormEdit')->name('shipntrack.label.master.edit');

//SNT Label
Route::get('shipntrack/label', 'shipntrack\Operations\Label\ShipnTrackLabelManagementController@index')->name('shipntrack.label.index');
Route::get('shipntrack/label/template/{destination}/{id}', 'shipntrack\Operations\Label\ShipnTrackLabelManagementController@LabelPdfTemplateShow')->name('shipntrack.label.template.show');
Route::get('shipntrack/label/pdf/download/{destination}/{id}', 'shipntrack\Operations\Label\ShipnTrackLabelManagementController@LabelPdfDownload')->name('shipntrack.label.download');
Route::get('shipntrack/label/fetch/record/{id}', 'shipntrack\Operations\Label\ShipnTrackLabelManagementController@LabelDetails')->name('shipntrack.label.details');
Route::post('shipntrack/label/edit', 'shipntrack\Operations\Label\ShipnTrackLabelManagementController@LabelEdit')->name('shipntrack.label.edit');

/*In-Scan  */
Route::get('shipntrack/in-scan', 'shipntrack\Manifest\ShiptrackInScanController@index')->name('shipntrack.inscan');
Route::get('shipntrack/in-scan/get/details', 'shipntrack\Manifest\ShiptrackInScanController@get_details')->name('shipntrack.inscan.get');
Route::post('shipntrack/in-scan/store', 'shipntrack\Manifest\ShiptrackInScanController@store')->name('shipntrack.inscan.store');

/*Export-Manifest  */
Route::get('shipntrack/export/manifest', 'shipntrack\Manifest\ExportManifestController@index')->name('shipntrack.export');
Route::get('shipntrack/export/export/view', 'shipntrack\Manifest\ExportManifestController@export_view')->name('shipntrack.export.view');
Route::get('shipntrack/export/single/fetch', 'shipntrack\Manifest\ExportManifestController@single_fetch')->name('shipntrack.export.single.fetch');
Route::post('shipntrack/export/export/store', 'shipntrack\Manifest\ExportManifestController@export_store')->name('shipntrack.export.store');
Route::get('shipntrack/export/{id}/details_view', 'shipntrack\Manifest\ExportManifestController@details_view')->name('shipntrack.export.details_view');

/* Inward Shipment */
Route::get('shipntrack/inward', 'shipntrack\Manifest\InwardController@index')->name('shipntrack.inward');
Route::get('shipntrack/inward/view', 'shipntrack\Manifest\InwardController@inw_view')->name('shipntrack.inward.view');
Route::get('shipntrack/inward/get/data', 'shipntrack\Manifest\InwardController@inw_data_fech')->name('shipntrack.inward.get');
Route::post('shipntrack/inward/store', 'shipntrack\Manifest\InwardController@store')->name('shipntrack.inward.store');
Route::get('shipntrack/inward/verify', 'shipntrack\Manifest\InwardController@verify')->name('shipntrack.inward.verify');
Route::get('shipntrack/inward/{id}/view', 'shipntrack\Manifest\InwardController@edit_view')->name('shipntrack.inward.edit_view');

/* Outward Shipment */
Route::get('shipntrack/outward', 'shipntrack\Manifest\OutwardController@index')->name('shipntrack.outward');
Route::get('shipntrack/outward/fetch_data', 'shipntrack\Manifest\OutwardController@fetch_data')->name('shipntrack.outward.get');
Route::post('shipntrack/outward/store', 'shipntrack\Manifest\OutwardController@store')->name('shipntrack.outward.store');
