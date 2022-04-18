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

 


