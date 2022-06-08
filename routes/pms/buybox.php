<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::prefix('buybox/')->group(function () {

    Route::get('asin','buybox\BuyboxAsinMasterController@index');
});