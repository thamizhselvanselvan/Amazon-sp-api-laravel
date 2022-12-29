<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('maintenence/mode', 'Admin\MaintenanceModeController@index')->name('maintenence.mode.home');
Route::POST('maintenence/maintenance-mode/on/off', 'Admin\MaintenanceModeController@MaintenanceModeOnOff')->name('maintenence.mode.on.off');
