<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PMSPHPUnitTestController;
use App\Http\Controllers\Inventory\Master\Rack\InventoryRackController;

Route::resource('inventory/racks', 'Inventory\Master\Rack\InventoryRackController');

Route::post('rack/{id}', 'Inventory\Master\Rack\InventoryShelveController@getRack');
Route::resource('inventory/shelves', 'Inventory\Master\Rack\InventoryShelveController');

Route::get('inventory/bins/create/rack/{id?}', 'Inventory\Master\Rack\InventoryBinController@create')->name('inventory.bin.create');
Route::get('inventory/bins/create/rack/{rack_id?}/shelve/{shelve_id?}', 'Inventory\Master\Rack\InventoryBinController@create')->name('inventory.bin.create');
Route::post('Binrack/{id}', 'Inventory\Master\Rack\InventoryBinController@getBinRack');
Route::post('BinShelves/{id}', 'Inventory\Master\Rack\InventoryBinController@getBinRackShelve');
Route::post('Bins/{id}', 'Inventory\Master\Rack\InventoryBinController@getBinRackShelve');
Route::resource('inventory/bins', 'Inventory\Master\Rack\InventoryBinController');


Route::resource('inventory/sources', 'Inventory\Master\InventorySourceController');
Route::get('inventory/Index', 'Inventory\Master\InventorySourceController@index');
Route::resource('inventory/destinations', 'Inventory\Master\InventoryDestinationController');
Route::resource('inventory/disposes', 'Inventory\Master\InventoryDisposeController');


// Route::get('inventory/stocks','Inventory\StockController@stokes')->name('inventory.stocks');
Route::get('inventory/stocks', 'Inventory\StockController@dashboard')->name('inventory.stocks');
Route::get('inventory/list', 'Inventory\StockController@getlist');
Route::get('inventory/export', 'Inventory\StockController@eportinv')->name('inventory.export');


Route::post('json/{id}', 'Inventory\InventoryWarehouseController@CountryStateCity')->name('country.name');
Route::post('stateId/{id}', 'Inventory\InventoryWarehouseController@getState');
Route::resource('inventory/warehouses', 'Inventory\InventoryWarehouseController');


Route::resource('inventory/features', 'Inventory\InventoryFeaturesController');
Route::resource('inventory/inwardings', 'Inventory\Stock\InventoryInwardingController');


Route::post('vendor/{id}', 'Inventory\InventoryVendorController@getState');
Route::post('vendorstate/{id}', 'Inventory\InventoryVendorController@getCity');
Route::resource('inventory/vendors', 'Inventory\InventoryVendorController');


Route::get('shipment/select/view', 'Inventory\inwarding\InventoryShipmentController@selectView');
Route::get('shipment/autocomplete', 'Inventory\inwarding\InventoryShipmentController@autocomplete');
Route::post('shipment/storeshipment', 'Inventory\inwarding\InventoryShipmentController@storeshipment');
Route::get('shipment/select/region', 'Inventory\inwarding\InventoryShipmentController@selectregion');
Route::get('shipment/inward/view', 'Inventory\inwarding\InventoryShipmentController@inwardingdata')->name('shipments.view');
Route::get('shipment/single/view', 'Inventory\inwarding\InventoryShipmentController@singleview')->name('shipments.single_view');
Route::get('inventory/shipments/{id}/place', 'Inventory\inwarding\InventoryShipmentController@store');
Route::post('shipment/place', 'Inventory\inwarding\InventoryShipmentController@placeship');
Route::get('inventory/shipments/{id}/lable', 'Inventory\inwarding\InventoryShipmentController@printlable');
Route::post('shipment/lable/export-pdf', 'Inventory\inwarding\InventoryShipmentController@Exportlable');
Route::get('Shipment/download/{ship_id}', 'Inventory\inwarding\InventoryShipmentController@DownloadPdf');
Route::post('racks/{id}', 'Inventory\inwarding\InventoryShipmentController@getRack');
Route::post('Shelves/{id}', 'Inventory\inwarding\InventoryShipmentController@getShelve');
Route::post('Bins/{id}', 'Inventory\inwarding\InventoryShipmentController@getbin');
Route::resource('inventory/shipments', 'Inventory\inwarding\InventoryShipmentController');

Route::post('shipment/warehouseg/{id}', 'Inventory\Outwarding\InventoryOutwardShipmentController@autofinish');
Route::get('shipment/autofinish', 'Inventory\Outwarding\InventoryOutwardShipmentController@autofinish');
Route::post('shipment/storeoutshipment', 'Inventory\Outwarding\InventoryOutwardShipmentController@storeoutshipment');
Route::get('shipment/select/View', 'Inventory\Outwarding\InventoryOutwardShipmentController@selectview');
Route::get('shipment/outwarding/view', 'Inventory\Outwarding\InventoryOutwardShipmentController@outwardingview')->name('outwarding.view');
Route::get('inventory/outwardings/{id}/outship', 'Inventory\Outwarding\InventoryOutwardShipmentController@outstore');
Route::resource('inventory/outwardings', 'Inventory\Outwarding\InventoryOutwardShipmentController');


Route::get('reports/daily', 'Inventory\ReportController@daily');
Route::get('reports/weekly', 'Inventory\ReportController@weekly');
Route::get('export/weekly', 'Inventory\ReportController@eportinvweekly');
Route::get('reports/monthly', 'Inventory\ReportController@monthly');
Route::get('export/monthly', 'Inventory\ReportController@eportinvmonthly');

Route::resource('inventory/reports', 'Inventory\ReportController');
