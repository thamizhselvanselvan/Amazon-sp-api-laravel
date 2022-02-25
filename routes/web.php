<?php

use App\Models\User;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use App\Models\universalTextile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use RedBeanPHP\R;

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

Route::get('login', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('login');
Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('home');

// Route::group(['middleware' => ['role:Admin', 'auth'], 'prefix' => 'admin'],function(){

// Route::get('dashboard', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('admin.dashboard');

// });

Route::resource('admin/mws_regions', 'Admin\RegionController');
Route::get('admin/credentials', 'Admin\CredentialsController@index');
Route::get('admin/currencys', 'Admin\CurrencyController@index');
Route::get('admin/rolespermissions', 'Admin\RolesPermissionsController@index');

Route::get('asin-master', 'AsinMasterController@index');
Route::get('add-asin', 'AsinMasterController@addAsin');
Route::get('import-bulk-asin', 'AsinMasterController@importBulkAsin');
Route::post('add-bulk-asin', 'AsinMasterController@addBulkAsin');

Route::resource('textiles', 'textilesController');
Route::post('import-csv', 'textilesController@importTextiles')->name('import.csv');
Route::get('export_to_csv', 'textilesController@exportTextilesToCSV')->name('export.csv');

Route::get('file_downloads', 'filedownloads\FileDownloadsController@filedownloads')->name('file.downloads');
Route::get('universalTextiles_download', function () {

     $file_path = "excel/downloads/universalTextilesExport.csv";
     //$path = Storage::path($file_path);
     if (Storage::exists($file_path)) {
          return Storage::download($file_path);
     }
     return 'file not exist';
})->name('download.universalTextiles');

Route::get('product/amazon_com', 'product\productController@index')->name('product.amazon_com');
Route::get('product/fetch_from_amazon', 'product\productController@fetchFromAmazon')->name('product.fetch.amazon');

Route::get('path', function () {

     $file_path = "excel/downloads/universalTextilesExport.csv";
     echo Storage::path($file_path);
     echo "<hr>";
     echo "Base Path:- ";
     echo base_path();

     echo "<hr>";
     echo 'saving path :- ';
     $file_path = "excel\\downloads\\universalTextilesExport.csv";
     echo Storage::path($file_path);

     //echo Str;
});


Route::resource('/tests', 'TestController');
Route::get('/test', function () {

     $path = 'universalTextilesImport/textiles.csv';

     return Storage::url($path);

     return ('downloaded done');
});

Route::get('/remove', function () {

     universalTextile::truncate();
});

Route::get('product/catalog-count', function () {

     $result = DB::select('select count(*) from amazon');

     return $result;
});

Route::get('/pdo', function () {

     $host = config('app.host');
     $dbname = config('app.database');
     $port = config('app.port');
     $username = config('app.username');
     $password = config('app.password');


     try {
          po($host);
          po($dbname);
          po($port);
          po($username);
          po($password);
          //$db = new PDO('mysql: host=' . $host . '; dbname=' . $dbname . ';port=' . $port, $username, $password);
          R::setup('mysql: host='.$host.'; dbname='.$dbname.';port='.$port, $username, $password); 
     } catch (PDOException $e) {
          echo $e->getmessage();
     } finally {
          echo 'working';
     }
});


include_route_files(__DIR__ . '/pms/');
