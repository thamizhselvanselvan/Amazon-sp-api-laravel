<?php

use Illuminate\Support\Facades\Route;

Route::get('admin/scheduler/management', 'Admin\ScheduleCommandController@index')->name('command.scheduler.index');
Route::get('admin/scheduler/management/edit/{id}', 'Admin\ScheduleCommandController@SchedulerEditForm')->name('command.scheduler.form.edit');
Route::get('admin/scheduler/management/remove/{id}', 'Admin\ScheduleCommandController@SchedulerFromTrash')->name('command.scheduler.form.trash');
Route::get('admin/scheduler/management/trash', 'Admin\ScheduleCommandController@SchedulerBin')->name('command.scheduler.form.bin');
Route::get('admin/scheduler/management/restore/{id}', 'Admin\ScheduleCommandController@SchedulerRestore')->name('command.scheduler.form.restore');
Route::post('admin/scheduler/management/submit', 'Admin\ScheduleCommandController@FormSubmit')->name('command.scheduler.form.submit');
Route::post('admin/scheduler/management/update', 'Admin\ScheduleCommandController@SchedulerFromUpdate')->name('command.scheduler.form.update');
