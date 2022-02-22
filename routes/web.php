<?php

use App\Models\User;
use App\Models\Mws_region;
use App\Models\universalTextile;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('/');

Auth::routes();

// Route::group(['middleware' => ['role:Admin', 'auth'], 'prefix' => 'admin'],function(){

// Route::get('dashboard', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('admin.dashboard');

// });

Route::resource('admin/mws_regions', 'Admin\RegionController');
Route::get('admin/credentials', 'Admin\CredentialsController@index');
Route::get('admin/currencys', 'Admin\CurrencyController@index');
Route::get('admin/rolespermissions', 'Admin\RolesPermissionsController@index');

Route::resource('textiles','textilesController');
Route::post('import-csv','textilesController@importTextiles')->name('import.csv');
Route::get('export_to_csv', 'textilesController@exportTextilesToCSV')->name('export.csv');

Route::get('file_downloads', 'filedownloads\FileDownloadsController@filedownloads')->name('file.downloads');
Route::get('universalTextiles_download', function(){

     $file_path = "excel/downloads/universalTextilesExport.csv";
     //$path = Storage::path($file_path);
     return Storage::download($file_path);
     //return response()->download($path);
     
     
     
})->name('download.universalTextiles');

Route::get('path', function(){
     
     $file_path = "excel/downloads/universalTextilesExport.csv";
     echo Storage::path($file_path);
     echo "<hr>";
     echo "Base Path:- ";
     echo base_path();

     echo "<hr>";
     echo 'saving path :- ';
     $file_path = "excel\\downloads\\universalTextilesExport.csv";
     echo Storage::path($file_path);
});

Route::get('login', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('login');
Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('home');


Route::resource('/tests', 'TestController');
Route::get('/test',function(){

     $path = 'universalTextilesImport/textiles.csv';

     return Storage::url($path);

     return('downloaded done');
});

Route::get('/remove', function(){

     universalTextile::truncate();
});

include_route_files(__DIR__ . '/pms/');