<?php

use Illuminate\Support\Facades\Route;

Route::get('invoice/manage', 'invoice\InvoiceManagementController@Index')->name('invoice.index');

Route::get('invoice/search-invoice', 'invoice\InvoiceManagementController@SearchInvoice')->name('invoice.search_invoice');
Route::match(['get', 'post'], 'invoice/select-invoice', 'invoice\InvoiceManagementController@SearchDateWiseInvoice');
// Route::get('invoice/upload', 'invoice\InvoiceManagementController@Upload');
// Route::post('invoice/upload/excel', 'invoice\InvoiceManagementController@UploadExcel');
Route::get('invoice/template', 'invoice\InvoiceManagementController@showpdf');
Route::get('invoice/convert-pdf/{id}', 'invoice\InvoiceManagementController@showTemplate');
Route::post('invoice/export-pdf', 'invoice\InvoiceManagementController@ExportPdf')->name('export.invoice.pdf');
Route::get('invoice/download/{invoice_no}', 'invoice\InvoiceManagementController@DownloadPdf')->name('invoice.download.pdf');
Route::get('invoice/download-direct/{id}', 'invoice\InvoiceManagementController@DirectDownloadPdf');
Route::get('invoice/download-all', 'invoice\InvoiceManagementController@DownloadAll');
Route::post('invoice/select-download', 'invoice\InvoiceManagementController@SelectedDownload')->name('invoice.download.selected');
Route::get('invoice/zip-download/{arr}', 'invoice\InvoiceManagementController@zipDownload');
Route::get('invoice/selected-print/{id}', 'invoice\InvoiceManagementController@selectedPrint');
Route::get('invoice/template/download', 'invoice\InvoiceManagementController@downloadTemplate')->name('invoice.download.template');
Route::get('invoice/edit/{id}', 'invoice\InvoiceManagementController@edit');
Route::post('invoice/update/{id}', 'invoice\InvoiceManagementController@update')->name('invoice.update');
Route::post('invoice/zip/download', 'invoice\InvoiceManagementController@zipDownload')->name('invoice.zip.download');
Route::get('invoice/zip/download/{mode}/{date}/zip/{file}', 'invoice\InvoiceManagementController@zipDownloadLink');
Route::get('invoice/file/management/monitor', 'invoice\InvoiceManagementController@InvoiceFileManagementMonitor')->name('invoice.file.management');

Route::get('invoice/upload/csv', 'invoice\InvoiceManagementController@UploadCsv')->name('invoice.upload.csv');
Route::post('invoice/csv/upload', 'invoice\InvoiceManagementController@InvoiceCsvFileUpload')->name('invoice.csv.file.import');
