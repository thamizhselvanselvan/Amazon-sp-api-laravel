<?php

use App\Models\Mws_region;
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
Route::post('inventory/vendorstate/{id}', 'Inventory\InventoryVendorController@getCity');
Route::resource('inventory/vendors', 'Inventory\InventoryVendorController');

/* Inward Shiment  */
Route::get('shipment/select/view', 'Inventory\inwarding\InventoryShipmentController@selectView');
Route::get('shipment/autocomplete', 'Inventory\inwarding\InventoryShipmentController@autocomplete');
Route::post('shipment/upload', 'Inventory\inwarding\InventoryShipmentController@autocomplete');
Route::post('shipment/upload/refresh', 'Inventory\inwarding\InventoryShipmentController@refreshtable');
Route::post('shipment/storeshipment', 'Inventory\inwarding\InventoryShipmentController@storeshipment');
Route::get('shipment/select/region', 'Inventory\inwarding\InventoryShipmentController@selectregion');
Route::get('shipment/single/view', 'Inventory\inwarding\InventoryShipmentController@singleview')->name('shipments.single_view');
Route::get('inventory/shipments/{source}/{id}/place', 'Inventory\inwarding\InventoryShipmentController@store');
Route::post('shipment/place', 'Inventory\inwarding\InventoryShipmentController@placeship');
Route::get('inventory/shipments/{source}/{id}/lable', 'Inventory\inwarding\InventoryShipmentController@printlable');
Route::post('shipment/lable/export-pdf', 'Inventory\inwarding\InventoryShipmentController@Exportlable');
Route::get('inventory/Shipment/download/{ship_id}', 'Inventory\inwarding\InventoryShipmentController@DownloadPdf');
Route::post('racks/{id}', 'Inventory\inwarding\InventoryShipmentController@getRack');
Route::post('Shelves/{id}', 'Inventory\inwarding\InventoryShipmentController@getShelve');
Route::post('Bins/{id}', 'Inventory\inwarding\InventoryShipmentController@getbin');
Route::get('inventory/shipments/{source}/{id}', 'Inventory\inwarding\InventoryShipmentController@show');
Route::resource('inventory/shipments', 'Inventory\inwarding\InventoryShipmentController');


/* Outward Shiment  */
Route::post('inventory/shipment/warehouseg/{id}', 'Inventory\Outwarding\InventoryOutwardShipmentController@autofinish');
Route::get('inventory/shipment/autofinish', 'Inventory\Outwarding\InventoryOutwardShipmentController@autofinish');
Route::post('inventory/shipment/storeoutshipment', 'Inventory\Outwarding\InventoryOutwardShipmentController@storeoutshipment')->name('inventory.out.save');
Route::get('inventory/shipment/select/View', 'Inventory\Outwarding\InventoryOutwardShipmentController@selectview')->name('inventory.shipment.select.View');
Route::get('inventory/shipment/outwarding/view', 'Inventory\Outwarding\InventoryOutwardShipmentController@outwardingview')->name('outwarding.view');
Route::get('inventory/outwardings/{id}/outship', 'Inventory\Outwarding\InventoryOutwardShipmentController@outstore')->name('inv.new.out');
Route::resource('inventory/outwardings', 'Inventory\Outwarding\InventoryOutwardShipmentController');

/* Report  */
Route::get('inventory/reports/daily', 'Inventory\ReportController@daily')->name('inventory.report.daily');
Route::get('inventory/reports/weekly', 'Inventory\ReportController@index')->name('inentory.report.weekly');
Route::get('inventory/export/weekly', 'Inventory\ReportController@exportinvweekly')->name('inventory.export.weekly');
Route::get('inventory/export/weekly/display', 'Inventory\ReportController@diaplayinvweekly')->name('inventory.export.weekly.display');
Route::get('inventory/export/weekly/warehousewise', 'Inventory\ReportController@expinvweeklywarewise')->name('inventory.export.weekly.warehouse');
Route::get('inventory/export/weekly/weekwareexpo/{id}', 'Inventory\ReportController@downexpwarewise')->name('export.weekly.weekwareexpo');
Route::get('inventory/tag/weekly/display', 'Inventory\ReportController@tagdisplay')->name('inventory.tag.weekly.display');
Route::get('inventory/export/weekly/tagwise', 'Inventory\ReportController@tagexprt')->name('inventory.tagswise.weekly.export');
Route::get('inventory/export/weekly/tags/{id}', 'Inventory\ReportController@downexptagwise')->name('inventory.weekly.tagrepo.download');
Route::get('inventory/export/daily', 'Inventory\ReportController@eportdaily')->name('inventory.export.daily');
Route::get('inventory/reports/monthly', 'Inventory\ReportController@monthlyview')->name('monthly.view');
Route::get('inventory/export/monthly', 'Inventory\ReportController@eportinvmonthly')->name('inventory.export.monthly');
Route::get('inventory/warewise', 'Inventory\ReportController@warerepo')->name('inventory.warewise.export');
Route::get('inventory/tagwise', 'Inventory\ReportController@tagwise')->name('inventory.tagwise.new');
Route::get('inventory/export/monthly/display','Inventory\ReportController@monthlywaredisp')->name('inventory.monthly.ware.display');
Route::get('inventory/export/monthly/warehousewise','Inventory\ReportController@monthlywareexpo')->name('inventory.monthly.ware.export');
Route::get('inventory/export/monthly/weekwareexpo/local/{id}','Inventory\ReportController@monthlywareexplocal')->name('inventory.monthly.ware.export.download');
Route::get('inventory/tag/monthly/display','Inventory\ReportController@monthtagrepdisp')->name('inventory.monthly.tag.report.display');
Route::get('inventory/tag/monthly/export','Inventory\ReportController@monthtagrepexport')->name('inventory.monthly.tag.report.export');
Route::get('inventory/tag/monthly/download/{id}', 'Inventory\ReportController@monthlytagexplocal')->name('inventory.monthly.tag.report.download');
Route::resource('inventory/reports', 'Inventory\ReportController');

//auth Test For AWS creds

// Route::get('inv/test',function(){
//     $mws_region = Mws_region::with('aws_verified')->where('region_code', 'IN')->first();
//     $test = !is_null($mws_region->aws_verified->first()) ? $mws_region->aws_verified->first()->auth_code : null;
// });