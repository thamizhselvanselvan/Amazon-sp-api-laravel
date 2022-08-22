<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('orders/dashboard', 'Orders\OrdersDashboardController@Dashboard');
Route::get('orders/list', 'Orders\OrdersListController@index');
Route::get('orders/getlist', 'Orders\OrdersListController@GetOrdersList')->name('getOrder.list');
Route::get('orders/itemsdetails', 'Orders\OrdersListController@GetOrderitems')->name('getOrderitem.list');

Route::get('orders/details', 'Orders\OrdersListController@OrderDetails');
Route::get('orders/item-details', 'Orders\OrdersListController@OrderItemDetails');
Route::get('orders/getdetails/', 'Orders\OrdersListController@GetOrderDetails')->name('getOrder.details');

Route::get('orders/item/dashboard', 'Orders\OrdersDashboardController@OrderItemDashboard');
