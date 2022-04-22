<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PMSPHPUnitTestController;
use App\Http\Controllers\Inventory\Master\Rack\InventoryRackController;



Route::get('inventory/master/index', 'Inventory\InventoryMasterController@IndexView');
Route::get('inventory/features/Index', 'Inventory\InventoryFeaturesController@FeaturesIndex');
Route::get('inventory/reporting/Index', 'Inventory\InventoryReportingController@ReportingIndex');
Route::get('inventory/Stock/Index', 'Inventory\InventoryStockController@StockIndex');
Route::get('inventory/System/Index', 'Inventory\InventorySystemController@SystemIndex');

Route::get('Inventory/Roles/Index', 'Inventory\InventoryMasterController@RolesView');

Route::get('Inventory/Master/Users/Index', 'Inventory\Master\InventoryUserController@UsersView')->name('index.show');
Route::get('Inventory/Master/Users/Add', 'Inventory\Master\InventoryUserController@create')->name('create_user.create');
Route::post('admin/admin/save_user', 'Admin\AdminManagementController@save_user')->name('inventory_save_user');


/**
 * 
 Route::get('inventory/racks/rack_list', 'Inventory\Master\InventoryRackController@index')->name('inv.rack_list');
 */

 
Route::resource('inventory/racks', 'Inventory\Master\Rack\InventoryRackController');
Route::resource('inventory/shelves', 'Inventory\Master\Rack\InventoryShelveController');
Route::resource('inventory/bins', 'Inventory\Master\Rack\InventoryBinController');

//  Route::get('inventory/racks','Inventory\Master\Rack\InventoryRackController@index')->name('inventory.rack.index');
//  Route::get('inventory/racks/create','Inventory\Master\Rack\InventoryRackController@create')->name('inventory.rack.create');
//  Route::post('inventory/racks','Inventory\Master\Rack\InventoryRackController@store')->name('inventory.rack.store');
//  Route::get('inventory/racks/{id}/edit', 'Inventory\Master\Rack\InventoryRackController@edit')->name('inventory.rack.edit');
//  Route::put('inventory/racks/{id}','Inventory\Master\Rack\InventoryRackController@update')->name('inventory.rack.update');
//  Route::delete('inventory/racks/{id}', 'Inventory\Master\Rack\InventoryRackController@destroy')->name('inventory.rack.destroy');

//  Route::get('inventory/shelves', 'Inventory\Master\Rack\InventoryshelvesController@index')->name('inventory.shelve.index');
//  Route::get('inventory/shelves/create','Inventory\Master\Rack\InventoryshelvesController@create')->name('inventory.shelve.create');
//  Route::post('inventory/shelves','Inventory\Master\Rack\InventoryshelvesController@save')->name('inventory.shelve.store');
//  Route::get('inventory/shelves/{id}/edit', 'Inventory\Master\Rack\InventoryshelvesController@edit')->name('inventory.shelve.edit');
//  Route::put('inventory/shelves/{id}','Inventory\Master\Rack\InventoryshelvesController@update')->name('inventory.shelve.update');
//  Route::delete('inventory/Shelve/{id}', 'Inventory\Master\Rack\InventoryshelvesController@destory')->name('inventory.shelve.destory');



 
//  Route::get('inventory/master/racks/bin/index','Inventory\Master\Rack\InventoryBinController@view')->name('inventory.bin_index');;
//  Route::get('inventory/master/racks/bin/add','Inventory\Master\Rack\InventoryBinController@add')->name('inventory.bin_add');
//  Route::post('inventory/master/racks/bin/save_bin','Inventory\Master\Rack\InventoryBinController@save')->name('inventory.bin_save');
//  Route::get('inventory/master/racks/bin/bin_list', 'Inventory\Master\Rack\InventoryBinController@index')->name('inv.bin_list');
//  Route::get('inventory/master/racks/bin/Bin/Edit_bin/{id}', 'Inventory\Master\Rack\InventoryBinController@edit');
//  Route::put('inventory/master/racks/bin/edit_bin/{id}','Inventory\Master\Rack\InventoryBinController@update')->name('inv.bin_update');
//  Route::post('Bin/delete/{id}', 'Inventory\Master\Rack\InventoryBinController@trash');



//  Route::get('Inventory/Master/Company/Index','Inventory\Master\InventoryCompanyController@companyview')->name('inventory.company_index');;
//  Route::get('Inventory/Master/Company/Add','Inventory\Master\InventoryCompanyController@companyadd')->name('inventory.company_add');

//  Route::get('Inventory/Master/Source/Index','Inventory\Master\InventorySourceController@sourceview')->name('inventory.source_index');;
//  Route::get('Inventory/Master/Source/Add','Inventory\Master\InventorySourceController@sourceadd')->name('inventory.source_add');

//  Route::get('Inventory/Master/Destination/Index','Inventory\Master\InventoryDestinationController@destinationview')->name('inventory.destination_index');;
//  Route::get('Inventory/Master/Destination/Add','Inventory\Master\InventoryDestinationController@destinationadd')->name('inventory.destination_add');



Route::get('inventory/Index','Inventory\Master\InventorySourceController@index');
