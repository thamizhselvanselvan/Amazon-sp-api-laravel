<?php

use Illuminate\Support\Facades\Route;

$method = ['get', 'post'];

Route::get('shipntrack/courier/tracking', 'shipntrack\Tracking\CourierTrackingController@index')->name('shipntrack.courier.tracking');
Route::get('shipntrack/courier/moredetails/{sourceDestination}/{awbno}', 'shipntrack\Tracking\CourierTrackingController@PacketMoreDetails');
Route::get('shipntrack/smsa/upload', 'shipntrack\Tracking\CourierTrackingController@uploadAwb')->name('shipntrack.smsa.upload');
Route::post('shipntrack/smsa/gettracking', 'shipntrack\Tracking\CourierTrackingController@GetTrackingDetails')->name('shipntrack.smsa.gettracking');
Route::get('shipntrack/courier/track', 'shipntrack\Tracking\CourierTrackingController@getDetails')->name('shipntrack.courier.track');

Route::get('shipntrack/bombino', 'shipntrack\Bombino\BombinoExpressController@index')->name('shipntrack.bombino');
Route::get('shipntrack/bombino/upload', 'shipntrack\Bombino\BombinoExpressController@upload')->name('shipntrack.bombino.upload');
Route::post('shipntrack/bombino/gettracking', 'shipntrack\Bombino\BombinoExpressController@getTracking')->name('shipntrack.bombino.gettracking');


Route::match($method, 'shipntrack/forwarder', 'shipntrack\Forwarder\ForwarderPacketMappingController@index')->name('shipntrack.forwarder');
Route::get('shipntrack/forwarder/template/download', 'shipntrack\Forwarder\ForwarderPacketMappingController@templateDownload')->name('shipntrack.forwarder.template');
Route::get('shipntrack/forwarder/upload', 'shipntrack\Forwarder\ForwarderPacketMappingController@Upload')->name('shipntrack.forwarder.upload');
Route::post('shipntrack/forwarder/save', 'shipntrack\Forwarder\ForwarderPacketMappingController@save')->name('shipntrack.forwarder.save');
Route::match($method, 'shipntrack/forwarder/search', 'shipntrack\Forwarder\ForwarderPacketMappingController@singlesearch')->name('shipntrack.forwarder.search');
Route::post('shipntrack/forwarder/update', 'shipntrack\Forwarder\ForwarderPacketMappingController@forwarderupdate')->name('shipntrack.forwarder.update');
//new 1
Route::post('shipntrack/forwarder/store/forwarder', 'shipntrack\Forwarder\ForwarderPacketMappingController@store_farwarder')->name('shipntrack.forwarder.store.forwarder');
Route::get('shipntrack/forwarder/select/view', 'shipntrack\Forwarder\ForwarderPacketMappingController@courierget')->name('shipntrack.forwarder.select.view');

Route::get('shipntrack/forwarder/mapped/details', 'shipntrack\Forwarder\ForwarderPacketMappingController@listing')->name('shipntrack.forwarder.mapped.details');

Route::get('shipntrack/missing/find', 'shipntrack\Forwarder\ForwarderPacketMappingController@missingexpview')->name('shipntrack.missing.find');
Route::get('shipntrack/missing/export', 'shipntrack\Forwarder\ForwarderPacketMappingController@missexport')->name('shipntrack.missing.export');
Route::get('shipntrack/missing/download', 'shipntrack\Forwarder\ForwarderPacketMappingController@downexp')->name('shipntrack.missing.download');

//shipment Edit
Route::get('shipntrack/shipment/edit/{destination}', 'shipntrack\Forwarder\ForwarderPacketMappingController@editshipment')->name('shipntrack.edit.shipment');
Route::get('shipntrack/shipment/data/edit', 'shipntrack\Forwarder\ForwarderPacketMappingController@editdata')->name('shipntrack.forwarder.edit.view');
Route::post('shipntrack/shipment/save/edit', 'shipntrack\Forwarder\ForwarderPacketMappingController@edit_store')->name('shipntrack.forwarder.save.edit');


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


Route::match($method, 'shipntrack/courier', 'shipntrack\Courier\CourierController@index')->name('shipntrack.courier.index');

Route::post('shipntrack/courier/store', 'shipntrack\Courier\CourierController@couriergsave')->name('snt.courier.store');
Route::get('shipntrack/courier/{id}/edit', 'shipntrack\Courier\CourierController@courieredit')->name('snt.courier.edit');
Route::post('shipntrack/courier/save/edit', 'shipntrack\Courier\CourierController@courierupdate')->name('snt.courier.update');
Route::get('shipntrack/courier/{id}/remove', 'shipntrack\Courier\CourierController@courierremove')->name('snt.courier.remove');




Route::match($method, 'shipntrack/partners', 'shipntrack\Courier\CourierPartnerController@index')->name('snt.courier.index');
Route::match($method, 'shipntrack/partners/create', 'shipntrack\Courier\CourierPartnerController@create')->name('courier.partners.create');
Route::match($method, 'shipntrack/partners/store', 'shipntrack\Courier\CourierPartnerController@store')->name('courier.partners.store');
Route::match($method, 'shipntrack/partners/remove/{id}', 'shipntrack\Courier\CourierPartnerController@destroy')->name('courier.partners.delete');
Route::match($method, 'shipntrack/partner/{id}/edit', 'shipntrack\Courier\CourierPartnerController@edit')->name('courier.partners.edit');
Route::match($method, 'shipntrack/partners/partner/update/{id}', 'shipntrack\Courier\CourierPartnerController@update')->name('courier.partners.update');


Route::get('shipntrack/booking', 'shipntrack\BookingMasterController@index')->name('snt.booking.index');
Route::post('shipntrack/booking/store', 'shipntrack\BookingMasterController@bookingsave')->name('snt.booking.store');
Route::get('shipntrack/booking/{id}/edit', 'shipntrack\BookingMasterController@bookingedit')->name('snt.booking.edit');
Route::post('shipntrack/booking/save/edit', 'shipntrack\BookingMasterController@bookingformedit')->name('snt.booking.update');
Route::get('shipntrack/booking/{id}/remove', 'shipntrack\BookingMasterController@bookingremove')->name('snt.booking.remove');


Route::get('shipntrack/status/manager', 'shipntrack\Courier\CourierStatusManagementController@index')->name('status.master.index');
Route::get('shipntrack/status/manager/{courier_id}', 'shipntrack\Courier\CourierStatusManagementController@index')->name('status.master.courier_id');
Route::get('shipntrack/status/store', 'shipntrack\Courier\CourierStatusManagementController@storestatus')->name('shipntrack.courier.status.store');

// POD
Route::get('shipntrack/b2c/POD', 'shipntrack\POD\B2cProofOfDeliveryController@index')->name('shipntrack_POD');
Route::get('shipntrack/b2c/templete', 'shipntrack\POD\B2cProofOfDeliveryController@templete')->name('shipntrack_templete');

// SNT Invoice
Route::get('shipntrack/invoice', 'shipntrack\Invoice\SNTInvoiceController@index')->name('shipntrack.invoice');
Route::get('shipntrack/invoice/{mode}', 'shipntrack\Invoice\SNTInvoiceController@index')->name('shipntrack.invoice.url');
Route::get('shipntrack/invoice/add/view', 'shipntrack\Invoice\SNTInvoiceController@create')->name('shipntrack.invoice.add.view');
Route::post('shipntrack/invoice/store', 'shipntrack\Invoice\SNTInvoiceController@store')->name('shipntrack.invoice.store');
Route::get('shipntrack/invoice/view/{invoice_no}', 'shipntrack\Invoice\SNTInvoiceController@invoiceview')->name('shipntrack.invoice.single.view');
Route::POST('shipntrack/invoice/export/pdf', 'shipntrack\Invoice\SNTInvoiceController@pdfexport')->name('export.shipntrack.invoice.pdf');
Route::get('shipntrack/invoice/dowload/pdf/{invoice_no}', 'shipntrack\Invoice\SNTInvoiceController@sntDownloadPdf')->name('export.shipntrack.invoice.download.pdf');
Route::get('shipntrack/invoice/download-direct/{invoice_no}', 'shipntrack\Invoice\SNTInvoiceController@directpdfdownload')->name('shipntrack.invoice.direct.download');
Route::get('shipntrack/invoice/edit/{invoice_no}', 'shipntrack\Invoice\SNTInvoiceController@invoiceeditview')->name('shipntrack.invoice.single.edit.view');
Route::post('shipntrack/invoice/edit/store', 'shipntrack\Invoice\SNTInvoiceController@invoiceeditstore')->name('shipntrack.invoice.edit.store');


Route::get('shipntrack/label', 'ShipnTrackLabelManagementController@index')->name('shipntrack.label.index');
Route::post('shipntrack/submit', 'ShipnTrackLabelManagementController@FormSubmit')->name('shipntrack.label.submit');
Route::get('shipntrack/label/template/{id}', 'ShipnTrackLabelManagementController@LabelPdfTemplate')->name('shipntrack.label.template');
Route::get('shipntrack/label/pdf/download/{id}', 'ShipnTrackLabelManagementController@LabelPdfDownload')->name('shipntrack.label.template');
