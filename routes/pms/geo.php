<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\Admin\Geo\GeoManagementController;
use App\Models\Inventory\Country;



Route::get('admin/geo/country', 'Admin\Geo\GeoManagementController@index_country')->name('geo.country');

Route::get('admin/geo/country/add', 'Admin\Geo\GeoManagementController@add_country')->name('geo.country.add');

Route::get('admin/geo/state/add', 'Admin\Geo\GeoManagementController@add_state')->name('geo.state.add');

Route::get('admin/geo/city/add', 'Admin\Geo\GeoManagementController@add_city')->name('geo.city.add');

Route::get('admin/geo/state', 'Admin\Geo\GeoManagementController@index_state')->name('geo.state.index');

Route::get('admin/geo/city', 'Admin\Geo\GeoManagementController@index_city')->name('geo.city.index');

Route::post('store_country', 'Admin\Geo\GeoManagementController@store_country')->name('geo.store.country');

Route::post('store_state', 'Admin\Geo\GeoManagementController@store_state')->name('geo.store.state');

Route::post('store_city', 'Admin\Geo\GeoManagementController@store_city')->name('geo.store.city');

Route::get('show_country', 'Admin\Geo\GeoManagementController@show_country');

Route::get('show_state', 'Admin\Geo\GeoManagementController@show_state');

Route::get('show_city', 'Admin\Geo\GeoManagementController@show_city');

Route::get('/delete_country/{id}', 'Admin\Geo\GeoManagementController@destroy_country');

Route::get('/delete_state/{id}', 'Admin\Geo\GeoManagementController@destroy_state');

Route::get('/delete_city/{id}', 'Admin\Geo\GeoManagementController@destroy_city');

Route::get('/edit_country/{id}', 'Admin\Geo\GeoManagementController@edit_country');

Route::post('/update_country/{id}', 'Admin\Geo\GeoManagementController@update_country');

Route::get('/edit_state/{id}', 'Admin\Geo\GeoManagementController@edit_state');

Route::post('/update_state/{id}', 'Admin\Geo\GeoManagementController@update_state');

Route::get('/edit_city/{id}', 'Admin\Geo\GeoManagementController@edit_city');

Route::post('/update_city/{id}', 'Admin\Geo\GeoManagementController@update_city');

Route::get('country/get', 'Admin\Geo\GeoManagementController@index_country')->name('country.get');

Route::get('state/get', 'Admin\Geo\GeoManagementController@index_state')->name('state.get');

Route::get('city/get', 'Admin\Geo\GeoManagementController@index_city')->name('city.get');
