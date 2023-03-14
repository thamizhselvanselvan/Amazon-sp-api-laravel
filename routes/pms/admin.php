<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;


Route::resource('admin/mws_regions', 'Admin\RegionController');
Route::get('admin/credentials', 'Admin\CredentialsController@index');
Route::get('admin/currencys', 'Admin\CurrencyController@index');
Route::get('admin/rolespermissions', 'Admin\RolesPermissionsController@index');

Route::get('admin/user_list', 'Admin\AdminManagementController@index')->name('admin.user_list');
Route::get('admin/password_reset_view/{id}', 'Admin\AdminManagementController@password_Change_view');
Route::post('admin/password_reset_save/{id}', 'Admin\AdminManagementController@password_reset_save')->name('admin.password_reset_save');
Route::get('admin/user/create', 'Admin\AdminManagementController@create')->name('add_user.create');
Route::post('admin/save_user', 'Admin\AdminManagementController@save_user')->name('admin_save_user');
Route::get('admin/{id}/edit', 'Admin\AdminManagementController@edit')->name('admin.edit');
Route::post('admin/update/{id}', 'Admin\AdminManagementController@update')->name('admin.update_user');
Route::get('admin/{id}/remove', 'Admin\AdminManagementController@delete');
Route::get('admin/bin', 'Admin\AdminManagementController@bin')->name('admin.bin');
Route::get('admin/role-restore/{id}', 'Admin\AdminManagementController@restore');


Route::get('admin/catalog_user', 'Admin\CatalogManagementController@index')->name('admin.catalog_user');
Route::get('admin/catalog/{id}/password_reset', 'Admin\CatalogManagementController@password_reset_view');
Route::post('admin/catalog/{id}/password_reset_save', 'Admin\CatalogManagementController@password_reset_save')->name('catalog.password_reset_save');
Route::get('admin/catalog/{id}/edit', 'Admin\CatalogManagementController@edit_view');

Route::post('admin/catalog/{id}/update', 'Admin\CatalogManagementController@update')->name('catalog_user.update');
Route::get('admin/catalog/create', 'Admin\CatalogManagementController@create')->name('catalog_user.create');
Route::post('admin/catalog/user_save', 'Admin\CatalogManagementController@user_save')->name('catalog_user_save');
Route::delete('admin/catalog/{id}/user_delete', 'Admin\CatalogManagementController@trash')->name('catalog_user_delete');

Route::get('admin/stores', 'Admin\AdminManagementController@selectStore')->name('admin.store');
Route::post('admin/update-store', 'Admin\AdminManagementController@updateStore');
Route::match(['get', 'post'], 'admin/stores/update', 'Admin\AdminManagementController@UpdatePushPriceColumn')->name('admin.store.update.push.price.column');
Route::get('admin/stores/edit/{id}', 'Admin\AdminManagementController@EditPushPriceColumn')->name('admin.store.edit.push.price.column');

Route::get('admin/system-setting', 'SystemSetting\SystemSettingController@index')->name('system.setting.home');
Route::post('admin/system-setting/add', 'SystemSetting\SystemSettingController@AddSystemSetting')->name('add.system.setting');
Route::get('admin/system-setting/edit/{id}', 'SystemSetting\SystemSettingController@EditSystemSetting')->name('edit.system.setting');
Route::post('admin/system-setting/update/{id}', 'SystemSetting\SystemSettingController@UpdateSystemSetting')->name('update.system.setting');
Route::get('admin/system-setting/remove/{id}', 'SystemSetting\SystemSettingController@DeleteSystemSetting')->name('remove.system.setting');
Route::get('admin/system-setting/recycle', 'SystemSetting\SystemSettingController@RecycleSystemSetting')->name('recycle.system.setting');
Route::get('admin/system-setting/restore/{id}', 'SystemSetting\SystemSettingController@RestoreSystemSetting')->name('restore.system.setting');


Route::get('admin/file-management', 'FileManagement\FileManagementController@index')->name('file.management.home');


Route::get('admin/job-management', 'Admin\JobsManagementController@index')->name('jobs.management.index');
Route::get('admin/job-management/exception', 'Admin\JobsManagementController@exceptiondetails')->name('jobs.management.exception');

Route::get('admin/process-management', 'Admin\ProcessManagementController@index')->name('process.management.index');


Route::get('admin/creds/manage/{id}', 'Admin\AdminManagementController@credentialmanage')->name('creds.manage');
Route::get('admin/creds/manage', 'Admin\AdminManagementController@credentialmanage')->name('creds.manage.id');
Route::get('admin/creds/save', 'Admin\AdminManagementController@credentialprioritysave')->name('save.creds.priority');
Route::get('admin/horizon/save', 'Admin\AdminManagementController@horizonprioritysave')->name('save.horizon.priority');

Route::get('admin/backup/management', 'Admin\DBbackupController@index')->name('admin.backup');
Route::get('admin/backup/save', 'Admin\DBbackupController@backupsave')->name('admin.backup.save');