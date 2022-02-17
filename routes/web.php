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
Route::get('logs-viewer', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
Route::resource('admin/mws_regions', 'Admin\RegionController');
Route::get('admin/credentials', 'Admin\CredentialsController@index');
Route::get('admin/currencys', 'Admin\CurrencyController@index');
Route::get('admin/rolespermissions', 'Admin\RolesPermissionsController@index');

Route::resource('textiles','textilesController');
Route::get('import-csv','textilesController@importTextiles')->name('import.csv');

Route::resource('/tests', 'TestController');


Route::get('login', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('login');
Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('home');


Route::get('/test',function(){

     $path = 'universalTextilesImport/textiles.csv';

     return Storage::path($path);

     return Storage::url($path);

//     $url ='https://files.channable.com/f8k02iylfY7c5YTsxH-SxQ==.csv';

//     $source = file_get_contents($url);
//    // file_put_contents('universalTextilesImport/textiles.csv', $source);
//     Storage::put('public/universalTextilesImport/textiles.csv',$source);

     return('downloaded done');
});

Route::get('/remove', function(){

     universalTextile::truncate();
});

include_route_files(__DIR__ . '/pms/');