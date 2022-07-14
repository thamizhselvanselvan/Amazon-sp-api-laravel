<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('amazon/invoice', 'AmazonInvoice\AmazonInvoiceManagementController@index');
Route::get('amazon/invoice/upload', 'AmazonInvoice\AmazonInvoiceManagementController@uploadInvoice')->name('amazon.invoice.upload');
Route::post('amazon/invoice/save', 'AmazonInvoice\AmazonInvoiceManagementController@invoiceSave')->name('amazon.invoice.save');
Route::get('amazon/invoice/view/{id}', 'AmazonInvoice\AmazonInvoiceManagementController@invoiceView')->name('amazon.invoice.view');