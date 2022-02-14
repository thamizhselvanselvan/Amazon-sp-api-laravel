<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Mws_region;
use App\Models\User;

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

Route::resource('/tests', 'TestController');


Route::get('login', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('login');
Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('home');


Route::get('/test',function(){

    $user = User::get();
    dd($user);

    $region = Mws_region::latest()->get();
    dd($region);
});