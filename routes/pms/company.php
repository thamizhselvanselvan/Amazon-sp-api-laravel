<?php

use Illuminate\Support\Facades\Route;

Route::get('company', 'Company\CompanyMasterController@index')->name('company');
Route::get('company/add', 'Company\CompanyMasterController@add')->name('company.add');
Route::post('company/create', 'Company\CompanyMasterController@create')->name('company.create');
Route::get('company/edit/{id}', 'Company\CompanyMasterController@edit')->name('company.edit');
Route::post('company/update/{id}', 'Company\CompanyMasterController@update')->name('company.update');
Route::post('company/trash/{id}', 'Company\CompanyMasterController@trash')->name('company.trash');
Route::get('company/trash-view', 'Company\CompanyMasterController@trashView')->name('company.trash-view');
Route::post('company/restore/{id}', 'Company\CompanyMasterController@restore')->name('company.restore');