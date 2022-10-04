<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PMSPHPUnitTestController;


/*  warehouse  */
Route::post('json/{id}', 'Inventory\InventoryWarehouseController@CountryStateCity')->name('country.name');
Route::post('stateId/{id}', 'Inventory\InventoryWarehouseController@getState');
Route::resource('inventory/warehouses', 'Inventory\InventoryWarehouseController');

/* Racks */
Route::resource('inventory/racks', 'Inventory\Master\Rack\InventoryRackController');

/* Shelves  */
Route::post('rack/{id}', 'Inventory\Master\Rack\InventoryShelveController@getRack');
Route::resource('inventory/shelves', 'Inventory\Master\Rack\InventoryShelveController');

/* Bins  */
Route::get('inventory/bins/create/rack/{id?}', 'Inventory\Master\Rack\InventoryBinController@create')->name('inventory.bin.make');
Route::get('inventory/bins/create/rack/{rack_id?}/shelve/{shelve_id?}', 'Inventory\Master\Rack\InventoryBinController@create')->name('inventory.bin.create');
Route::post('Binrack/{id}', 'Inventory\Master\Rack\InventoryBinController@getBinRack');
Route::post('BinShelves/{id}', 'Inventory\Master\Rack\InventoryBinController@getBinRackShelve');
Route::post('Bins/{id}', 'Inventory\Master\Rack\InventoryBinController@getBinRackShelve');
Route::resource('inventory/bins', 'Inventory\Master\Rack\InventoryBinController');
Route::resource('inventory/items', 'Inventory\Master\InventoryItemsController.');

/* Dispose  */
Route::resource('inventory/sources', 'Inventory\Master\InventorySourceController');
Route::get('inventory/Index', 'Inventory\Master\InventorySourceController@index');
Route::resource('inventory/destinations', 'Inventory\Master\InventoryDestinationController');
Route::resource('inventory/disposes', 'Inventory\Master\InventoryDisposeController');

/* Tags  */
Route::resource('inventory/tags', 'Inventory\Master\TagController');



/*  Stocks  */
Route::get('inventory/stocks', 'Inventory\StockController@dashboard')->name('inventory.stocks');
Route::get('inventory/list', 'Inventory\StockController@getlist');
Route::get('inventory/expo', 'Inventory\StockController@eportinv');
// Route::get('inventory/export', 'Inventory\StockController@eportinv')->name('inventory.export');
Route::get('inventory/exp/{id}', 'Inventory\StockController@downexp');


/* Under development  */
Route::resource('inventory/features', 'Inventory\InventoryFeaturesController');
Route::resource('inventory/inwardings', 'Inventory\Stock\InventoryInwardingController');

/* Vendor  */
Route::post('vendor/{id}', 'Inventory\InventoryVendorController@getState');
Route::post('vendorstate/{id}', 'Inventory\InventoryVendorController@getCity');
Route::resource('inventory/vendors', 'Inventory\InventoryVendorController');

/* Inward Shiment  */
Route::get('shipment/select/view', 'Inventory\inwarding\InventoryShipmentController@selectView');
Route::get('shipment/autocomplete', 'Inventory\inwarding\InventoryShipmentController@autocomplete');
Route::post('shipment/upload', 'Inventory\inwarding\InventoryShipmentController@autocomplete');
Route::post('shipment/upload/refresh', 'Inventory\inwarding\InventoryShipmentController@refreshtable');
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


/* Outward Shiment  */
Route::post('shipment/warehouseg/{id}', 'Inventory\Outwarding\InventoryOutwardShipmentController@autofinish');
Route::get('shipment/autofinish', 'Inventory\Outwarding\InventoryOutwardShipmentController@autofinish');
Route::post('shipment/storeoutshipment', 'Inventory\Outwarding\InventoryOutwardShipmentController@storeoutshipment');
Route::get('shipment/select/View', 'Inventory\Outwarding\InventoryOutwardShipmentController@selectview');
Route::get('shipment/outwarding/view', 'Inventory\Outwarding\InventoryOutwardShipmentController@outwardingview')->name('outwarding.view');
Route::get('inventory/outwardings/{id}/outship', 'Inventory\Outwarding\InventoryOutwardShipmentController@outstore');
Route::resource('inventory/outwardings', 'Inventory\Outwarding\InventoryOutwardShipmentController');

/* Report  */
Route::get('reports/daily', 'Inventory\ReportController@daily');
Route::get('reports/weekly', 'Inventory\ReportController@index');
Route::get('export/weekly', 'Inventory\ReportController@eportinvweekly');
Route::get('export/daily', 'Inventory\ReportController@eportdaily');
Route::get('reports/monthly', 'Inventory\ReportController@monthlyview')->name('monthly.view');
Route::get('export/monthly', 'Inventory\ReportController@eportinvmonthly');
Route::get('inventory/warewise', 'Inventory\ReportController@warerepo');
Route::get('inventory/tagwise', 'Inventory\ReportController@tagwise');
Route::resource('inventory/reports', 'Inventory\ReportController');
