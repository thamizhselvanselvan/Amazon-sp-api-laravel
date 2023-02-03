<?php

use Carbon\Carbon;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\Catalog\PriceConversion;
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

    $priceConversion = new PriceConversion();
    queryAgain:
    $records = PricingUs::query()
        ->select('asin', 'weight', 'us_price')
        ->where('status', '0')
        ->limit(100)
        ->get()
        ->toArray();

    $updatingRecord = [];
    foreach ($records as $record) {

        $convertedPrice = $priceConversion->USAToINDB2B($record['weight'], $record['us_price']);
        $updatingRecord[] = [
            'asin' => $record['asin'],
            'status' => 1,
            'weight' => $record['weight'],
            'us_price' => $record['us_price'],
            'usa_to_in_b2b' => $convertedPrice
        ];
    }
    PricingUs::upsert($updatingRecord, ['asin_unique'], ['asin', 'status', 'weight', 'us_price', 'usa_to_in_b2b']);
    $data = PricingUs::where('status', 0)->get()->count('id');

    if ($data != 0) {
        goto queryAgain;
    } else {
        // PricingUs::where('status', 1)->update(['status' => 0]);
    }
});

// uncomment this one it is commented
// include_route_files(__DIR__ . '/pms/');

