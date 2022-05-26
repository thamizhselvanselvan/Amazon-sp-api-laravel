<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PMSPHPUnitTestController;
use App\Http\Controllers\Inventory\Master\Rack\InventoryRackController;
 
Route::resource('inventory/racks', 'Inventory\Master\Rack\InventoryRackController');
Route::resource('inventory/shelves', 'Inventory\Master\Rack\InventoryShelveController');

Route::get('inventory/bins/create/rack/{id?}','Inventory\Master\Rack\InventoryBinController@create')->name('inventory.bin.create');
Route::get('inventory/bins/create/rack/{rack_id?}/shelve/{shelve_id?}','Inventory\Master\Rack\InventoryBinController@create')->name('inventory.bin.create');


Route::resource('inventory/bins', 'Inventory\Master\Rack\InventoryBinController');
Route::resource('inventory/sources', 'Inventory\Master\InventorySourceController');


Route::resource('inventory/destinations','Inventory\Master\InventoryDestinationController');
Route::resource('inventory/disposes','Inventory\Master\InventoryDisposeController');

Route::get('inventory/stocks','Inventory\Master\InventoryDisposeController@stokes')->name('inventory.stocks');

Route::resource('inventory/warehouses','Inventory\InventoryWarehouseController');
Route::resource('inventory/features','Inventory\InventoryFeaturesController');
Route::resource('inventory/inwardings','Inventory\Stock\InventoryInwardingController');
Route::resource('inventory/vendors','Inventory\InventoryVendorController');

Route::get('shipment/select/view', 'Inventory\inwarding\InventoryShipmentController@selectView');
Route::get('shipment/autocomplete', 'Inventory\inwarding\InventoryShipmentController@autocomplete');
Route::post('shipment/storeshipment', 'Inventory\inwarding\InventoryShipmentController@storeshipment');
Route::get('shipment/select/region', 'Inventory\inwarding\InventoryShipmentController@selectregion');
Route::get('shipment/inward/view', 'Inventory\inwarding\InventoryShipmentController@inwardingdata')->name('shipments.view');
Route::get('shipment/single/view', 'Inventory\inwarding\InventoryShipmentController@singleview')->name('shipments.single_view');
Route::resource('inventory/shipments','Inventory\inwarding\InventoryShipmentController');


Route::get('shipment/autofinish', 'Inventory\Outwarding\InventoryOutwardShipmentController@autofinish');
 Route::post('shipment/storeoutshipment', 'Inventory\Outwarding\InventoryOutwardShipmentController@storeoutshipment');
 Route::get('shipment/select/View', 'Inventory\Outwarding\InventoryOutwardShipmentController@selectview');
 Route::get('shipment/outwarding/view', 'Inventory\Outwarding\InventoryOutwardShipmentController@outwardingview')->name('outwarding.view');
 Route::resource('inventory/outwardings','Inventory\Outwarding\InventoryOutwardShipmentController');
 

Route::get('inventory/Index','Inventory\Master\InventorySourceController@index');
Route::post('json/{id}','Inventory\InventoryWarehouseController@CountryStateCity')->name('country.name');
Route::post('stateId/{id}','Inventory\InventoryWarehouseController@getState');
Route::post('vendor/{id}','Inventory\InventoryVendorController@getState');
Route::post('vendorstate/{id}','Inventory\InventoryVendorController@getCity');
