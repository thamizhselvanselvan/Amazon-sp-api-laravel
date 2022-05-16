<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PMSPHPUnitTestController;
use App\Http\Controllers\Inventory\Master\Rack\InventoryRackController;



// Route::get('inventory/master/index', 'Inventory\InventoryMasterController@IndexView');
// Route::get('inventory/features/Index', 'Inventory\InventoryFeaturesController@FeaturesIndex');
// Route::get('inventory/reporting/Index', 'Inventory\InventoryReportingController@ReportingIndex');
// Route::get('inventory/Stock/Index', 'Inventory\InventoryStockController@StockIndex');
// Route::get('inventory/System/Index', 'Inventory\InventorySystemController@SystemIndex');

// Route::get('Inventory/Roles/Index', 'Inventory\InventoryMasterController@RolesView');

// Route::get('Inventory/Master/Users/Index', 'Inventory\Master\InventoryUserController@UsersView')->name('index.show');
// Route::get('Inventory/Master/Users/Add', 'Inventory\Master\InventoryUserController@create')->name('create_user.create');
// Route::post('admin/admin/save_user', 'Admin\AdminManagementController@save_user')->name('inventory_save_user');
// Route::get('inventory/bin/index','Inventory\Master\Rack\InventoryBinController@rackselect')->name('inventory.bin_shelve');;

 
Route::resource('inventory/racks', 'Inventory\Master\Rack\InventoryRackController');
Route::resource('inventory/shelves', 'Inventory\Master\Rack\InventoryShelveController');

Route::get('inventory/bins/create/rack/{id?}','Inventory\Master\Rack\InventoryBinController@create')->name('inventory.bin.create');
Route::get('inventory/bins/create/rack/{rack_id?}/shelve/{shelve_id?}','Inventory\Master\Rack\InventoryBinController@create')->name('inventory.bin.create');


Route::resource('inventory/bins', 'Inventory\Master\Rack\InventoryBinController');
Route::resource('inventory/sources', 'Inventory\Master\InventorySourceController');


Route::resource('inventory/destinations','Inventory\Master\InventoryDestinationController');
Route::resource('inventory/disposes','Inventory\Master\InventoryDisposeController');

Route::resource('inventory/warehouses','Inventory\InventoryWarehouseController');
Route::resource('inventory/features','Inventory\InventoryFeaturesController');
Route::resource('inventory/inwardings','Inventory\Stock\InventoryInwardingController');

Route::resource('inventory/shipments','Inventory\inwarding\InventoryShipmentController');
Route::get('shipment/select/view', 'Inventory\inwarding\InventoryShipmentController@selectView');
Route::get('shipment/autocomplete', 'Inventory\inwarding\InventoryShipmentController@autocomplete');
Route::post('shipment/storeshipment', 'Inventory\inwarding\InventoryShipmentController@storeshipment');
Route::get('shipment/select/region', 'Inventory\inwarding\InventoryShipmentController@selectregion');


 Route::resource('inventory/outwardings','Inventory\outwarding\InventoryOutwardShipmentController');
 Route::get('shipment/autofinish', 'Inventory\outwarding\InventoryOutwardShipmentController@autofinish');
 Route::post('shipment/storeoutshipment', 'Inventory\outwarding\InventoryOutwardShipmentController@storeoutshipment');
 Route::get('shipment/select/View', 'Inventory\outwarding\InventoryOutwardShipmentController@selectview');


//  Route::get('inventory/master/racks/bin/index','Inventory\Master\Rack\InventoryBinController@view')->name('inventory.bin_index');;
//  Route::get('inventory/master/racks/bin/add','Inventory\Master\Rack\InventoryBinController@add')->name('inventory.bin_add');
//  Route::post('inventory/master/racks/bin/save_bin','Inventory\Master\Rack\InventoryBinController@save')->name('inventory.bin_save');
//  Route::get('inventory/master/racks/bin/bin_list', 'Inventory\Master\Rack\InventoryBinController@index')->name('inv.bin_list');
//  Route::get('inventory/master/racks/bin/Bin/Edit_bin/{id}', 'Inventory\Master\Rack\InventoryBinController@edit');
//  Route::put('inventory/master/racks/bin/edit_bin/{id}','Inventory\Master\Rack\InventoryBinController@update')->name('inv.bin_update');
//  Route::post('Bin/delete/{id}', 'Inventory\Master\Rack\InventoryBinController@trash');


Route::get('inventory/Index','Inventory\Master\InventorySourceController@index');
