<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Buybox_stores\Product_Push;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Modal;

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
// use ConfigTrait;

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');
Auth::routes();
Route::get('login', 'Admin\HomeController@dashboard')->name('login');
Route::get('home', 'Admin\HomeController@dashboard')->name('home');

// include_route_files(__DIR__ . '/pms/');
Route::get('testing', function () {
    $table = table_model_set(country_code: 'in', model: 'bb_product_aa_custom_seller_detail', table_name: 'product_aa_custom_p2_in_seller_detail');
    $data = $table->truncate();
    po($table->getTable());
});
