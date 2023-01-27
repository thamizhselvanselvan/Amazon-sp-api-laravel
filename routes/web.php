<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Buybox_stores\Product_Push;
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
Route::get('feed', function () {
    $store_id = 6;
    $products = Product_Push::query()
        ->select('product_sku', 'store_id', 'availability', 'push_price', 'base_price', 'latency')
        ->where('push_status', 0)
        ->where('store_id', $store_id)
        ->get()
        ->toArray();
    $feedData = [];
    foreach ($products as $product) {

        $feedData['seller_id'] = $store_id;
        $feedData['feedLists'] = $product;
        jobDispatchFunc($class, $forTranslation, $queue_name, $queue_delay);
    }
    po($feedData);
});
