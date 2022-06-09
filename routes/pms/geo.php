<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;


Route::get('admin/geo','Admin\Geo\GeoManagementController@index');