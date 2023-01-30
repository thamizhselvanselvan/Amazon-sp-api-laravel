<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
    $start = "'" . Carbon::now()->subHour()->toDateTimeString() . "'";
    $end = "'" . Carbon::now()->toDateTimeString() . "'";

    // po($start);
    // po($end);
    // exit;
    $limit = 5000;
    $product_seller_details = "bb_product_aa_custom_p2_us_seller_details";
    $product_lp = "bb_product_aa_custom_p2_us_offers";
    $destination_model = table_model_create(country_code: 'us', model: 'Asin_destination', table_name: 'asin_destination_');

    $data = $destination_model->select(['asin', 'user_id'])
        ->where('price_status', 0)->where('priority', 2)
        ->limit($limit)->get();

    $des_asin_array = [];
    foreach ($data as $value) {
        $des_asin_array[] = $value->asin;
    }

    $catalog_model = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');
    $cat_data = $catalog_model->select(['asin', 'dimensions'])->whereIn('asin', $des_asin_array)->get();
    $asin = [];

    foreach ($cat_data as $value) {
        $asin[] = "'$value->asin'";
    }
    $asin = implode(',', $asin);
    po($asin);
    // exit;
    $asin_price = DB::connection('buybox')
        ->select("SELECT PPO.asin, LP.available, 
                        LP.is_sold_by_amazon,
                        LP.is_any_our_seller_own_bb, 
                        LP.next_highest_seller_price,
                        LP.next_highest_seller_id,
                        LP.next_lowest_seller_price,
                        LP.next_lowest_seller_id,
                        LP.bb_winner_price,
                        LP.bb_winner_id,
                        LP.updated_at as updated_at,
                            GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                            group_concat(PPO.listingprice_amount) as listingprice_amount
                            FROM 
                                $product_seller_details as PPO
                                    JOIN
                                 $product_lp as LP 
                            ON 
                            PPO.asin = LP.asin
                            WHERE LP.updated_at BETWEEN $start AND $end 
                            AND
                            PPO.asin IN ($asin) 
                            
                            GROUP BY PPO.asin
                        ");
    po($asin_price);
});
