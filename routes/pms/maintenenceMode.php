<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('maintenence/maintenence-mode', 'Admin\MaintenanceModeController@index')->name('maintenance.mode.home');
Route::POST('maintenence/maintenance-mode/on/off', 'Admin\MaintenanceModeController@MaintenanceModeOnOff')->name('maintenance.mode.on.off');
