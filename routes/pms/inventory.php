<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;



Route::get('Inventory/master/Index', 'Inventory\InventoryMasterController@IndexView');
Route::get('Inventory/Features/Index', 'Inventory\InventoryFeaturesController@FeaturesIndex');
Route::get('Inventory/Reporting/Index', 'Inventory\InventoryReportingController@ReportingIndex');
Route::get('Inventory/Stock/Index', 'Inventory\InventoryStockController@StockIndex');
Route::get('Inventory/System/Index', 'Inventory\InventorySystemController@SystemIndex');

Route::get('Inventory/Roles/Index', 'Inventory\InventoryMasterController@RolesView');

Route::get('Inventory/Master/Users/Index', 'Inventory\Master\InventoryUserController@UsersView')->name('index.show');
Route::get('Inventory/Master/Users/Add', 'Inventory\Master\InventoryUserController@create')->name('create_user.create');
Route::post('admin/admin/save_user', 'Admin\AdminManagementController@save_user')->name('inventory_save_user');


 Route::get('Inventory/Master/Racks/Index','Inventory\Master\InventoryRackController@RacksView')->name('inventory.rack_index');;
 Route::get('Inventory/Master/Racks/Add','Inventory\Master\InventoryRackController@Racksadd')->name('inventory.rack_add');
 Route::post('Inventory/Master/Racks/save_rack','Inventory\Master\InventoryRackController@save_racks')->name('inventory.rack_save');
 Route::get('Inventory/Master/Racks/rack_list', 'Inventory\Master\InventoryRackController@index')->name('inv.rack_list');

 Route::get('Inventory/Master/Company/Index','Inventory\Master\InventoryCompanyController@companyview')->name('inventory.company_index');;
 Route::get('Inventory/Master/Company/Add','Inventory\Master\InventoryCompanyController@companyadd')->name('inventory.company_add');

 Route::get('Inventory/Master/Source/Index','Inventory\Master\InventorySourceController@sourceview')->name('inventory.source_index');;
 Route::get('Inventory/Master/Source/Add','Inventory\Master\InventorySourceController@sourceadd')->name('inventory.source_add');

 Route::get('Inventory/Master/Destination/Index','Inventory\Master\InventoryDestinationController@destinationview')->name('inventory.destination_index');;
 Route::get('Inventory/Master/Destination/Add','Inventory\Master\InventoryDestinationController@destinationadd')->name('inventory.destination_add');



 Route::get('Inventory/Master/Racks/Shelves/Index','Inventory\Master\InventoryRackController@Shelvesview')->name('inventory.Shelves_index');;
 Route::get('Inventory/Master/Shelves/Add','Inventory\Master\InventoryRackController@Shelvesadd')->name('inventory.Shelves_add');
