<?php

use Illuminate\Support\Facades\Route;


Route::get('admin/rate-master/index', 'Admin\RateMasterManagementController@Index')->name('rateMaster.index');
Route::get('admin/rate-master/upload', 'Admin\RateMasterManagementController@upload');
Route::post('admin/rate-master/upload/csv', 'Admin\RateMasterManagementController@uploadCsv');
Route::get('admin/rate-master/template/download', 'Admin\RateMasterManagementController@templateDownload');
Route::get('admin/rate-master/get', 'Admin\RateMasterManagementController@GetDataTable');
