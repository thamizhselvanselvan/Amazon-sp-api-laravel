<?php

use Illuminate\Support\Facades\Route;    

Route::get('invoice/manage', 'invoice\InvoiceManagementController@Index')->name('invoice.index');
Route::get('invoice/search-invoice', 'invoice\InvoiceManagementController@SearchInvoice')->name('invoice.search_invoice');
Route::post('invoice/select-invoice', 'invoice\InvoiceManagementController@SearchDateWiseInvoice');
Route::get('invoice/upload', 'invoice\InvoiceManagementController@Upload');
Route::post('invoice/upload/excel', 'invoice\InvoiceManagementController@UploadExcel');
Route::get('invoice/template', 'invoice\InvoiceManagementController@showpdf');
Route::get('invoice/convert-pdf/{id}', 'invoice\InvoiceManagementController@showTemplate');
Route::post('invoice/export-pdf','invoice\InvoiceManagementController@ExportPdf');
Route::get('invoice/download/{invoice_no}', 'invoice\InvoiceManagementController@DownloadPdf');
Route::get('invoice/download-direct/{id}', 'invoice\InvoiceManagementController@DirectDownloadPdf');
Route::get('invoice/download-all', 'invoice\InvoiceManagementController@DownloadAll');
Route::post('invoice/select-download', 'invoice\InvoiceManagementController@SelectedDownload');
Route::get('invoice/zip-download/{arr}', 'invoice\InvoiceManagementController@zipDownload');
Route::get('invoice/selected-print/{id}', 'invoice\InvoiceManagementController@selectedPrint');
Route::get('invoice/template/download', 'invoice\InvoiceManagementController@downloadTemplate');
Route::get('invoice/edit/{id}', 'invoice\InvoiceManagementController@edit');