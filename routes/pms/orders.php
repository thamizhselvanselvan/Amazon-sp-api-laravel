<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('orders/dashboard', 'Orders\OrdersDashboardController@Dashboard');
Route::get('orders/list', 'orders\OrdersListController@index');
Route::get('orders/getlist', 'orders\OrdersListController@GetOrdersList')->name('getOrder.list');
Route::get('orders/select-store', 'orders\OrdersListController@selectStore')->name('select.store');
Route::post('orders/update-store', 'orders\OrdersListController@updateStore');
Route::get('orders/itemsdetails', 'orders\OrdersListController@GetOrderitems')->name('getOrderitem.list');


Route::get('orders/details', 'orders\OrdersListController@OrderDetails');
Route::get('orders/item-details', 'orders\OrdersListController@OrderItemDetails');
Route::get('orders/getdetails/', 'orders\OrdersListController@GetOrderDetails')->name('getOrder.details');
