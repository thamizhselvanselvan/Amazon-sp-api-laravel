<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;




Route::get('v2/oms', 'V2\Oms\StatusMaster\OmsController@index')->name('oms.home');
Route::prefix('v2/oms/status-master')->group(function () {
    Route::post('/add', 'V2\Oms\StatusMaster\OmsController@AddStatusMaster')->name('add.oms.status');
    Route::get('/edit/{id}', 'V2\Oms\StatusMaster\OmsController@EditStatusMaster')->name('edit.oms.status');
    Route::post('/update/{id}', 'V2\Oms\StatusMaster\OmsController@UpdateStatusMaster')->name('update.oms.status');
    Route::get('/remove/{id}', 'V2\Oms\StatusMaster\OmsController@DeleteStatusMaster')->name('remove.oms.status');
    Route::get('/recycle', 'V2\Oms\StatusMaster\OmsController@RecycleStatusMaster')->name('recycle.oms.status');
    Route::get('/restore/{id}', 'V2\Oms\StatusMaster\OmsController@RestoreStatusMaster')->name('restore.oms.status');
});
