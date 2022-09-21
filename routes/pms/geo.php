<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\Admin\Geo\GeoManagementController;
use App\Models\Inventory\Country;



Route::get('admin/geo/country','Admin\Geo\GeoManagementController@index_country');

Route::get('admin/geo/country/add','Admin\Geo\GeoManagementController@add_country');

Route::get('admin/geo/state/add','Admin\Geo\GeoManagementController@add_state');

Route::get('admin/geo/city/add','Admin\Geo\GeoManagementController@add_city');

Route::get('admin/geo/state','Admin\Geo\GeoManagementController@index_state');

Route::get('admin/geo/city','Admin\Geo\GeoManagementController@index_city');

Route::post('store_country', 'Admin\Geo\GeoManagementController@store_country');

Route::post('store_state', 'Admin\Geo\GeoManagementController@store_state');

Route::post('store_city', 'Admin\Geo\GeoManagementController@store_city');

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