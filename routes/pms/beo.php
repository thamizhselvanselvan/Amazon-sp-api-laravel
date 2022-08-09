<?php

use Smalot\PdfParser\Parser;
use App\Services\BOE\BOEMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\BOE\BOEPdefreader2018;
use App\Http\Controllers\PMSPHPUnitTestController;

Route::get('BOE/index', 'BOE\BOEController@index');
Route::get('BOE/upload', 'BOE\BOEController@BOEPdfUploadView');
Route::post('BOE/bulk-upload', 'BOE\BOEController@BulkPdfUpload');
Route::get('BOE/pdf-reader', 'BOE\BOEController@BOEPDFReader');
Route::get('BOE/Export', 'BOE\BOEController@BOEExportToCSV');
Route::get('BOE/Export/view', 'BOE\BOEController@BOEExportView');
Route::post('BOE/Export/filter', 'BOE\BOEController@BOEFilterExport')->name('BOE.Export.Filter');

Route::get('BOE/Download', 'BOE\BOEController@Download_BOE');

Route::get('BOE/readfromfile', 'BOE\BOEController@ReadFromfile');
Route::get("BOE/upload/do", 'BOE\BOEController@Upload');
Route::get("BOE/remove", 'BOE\BOEController@RemoveUploadedFiles');
Route::get("BOE/report","BOE\BOEController@boeReport");


// Route::get('Boe/test', function()
// {
    
//     $pdfReader = new BOEMaster;
//     $content = '';
//      $storage_path = ' ';
//      $company_id = ' ' ;
//      $user_id = '';
//     $pdfReader->BOEmanage($content, $storage_path, $company_id, $user_id);
// });
Route::get('Boe/test', function()
{
    $pdfReader = new BOEPdefreader2018;
  
    $pdfReader->BOEPDFReaderold();
});

