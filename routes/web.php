<?php

use Carbon\Carbon;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\Catalog\PriceConversion;
use App\Models\Buybox_stores\Product_Push;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Modal;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;
use App\Services\AmazonFeedApiServices\AmazonFeedProcessAvailability;

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

include_route_files(__DIR__ . '/v2/');
Route::get('testing', function () {

    $test = (new FeedOrderDetailsApp360)->getFeedStatus(134725019440, 6);
    // $test = (new FeedOrderDetailsApp360)->getLists(6);

    // dd($test);

    $ggt = json_decode(json_encode(file_get_contents($test)));


    dd($ggt);

    // dd(json_encode([
    //     "productType" => "PRODUCT",
    //     "patches" => [
    //         [
    //             "op" => "replace",
    //             "operation_type" => "PARTIAL_UPDATE",
    //             "path" => "/attributes/fulfillment_availability",
    //             "value" => [
    //                 [
    //                     "fulfillment_channel_code" => "DEFAULT",
    //                     "quantity" => 1
    //                 ]
    //             ]
    //         ]
    //     ]
    //                 ]));

    $new_feed = (new AmazonFeedProcessAvailability)->listing(6, 'IN');

    dd($new_feed);

    $asins = DB::connection('catalog')->select("SELECT asin, user_id
    FROM asin_source_uss
    WHERE status='0'
    ORDER BY id DESC
    LIMIT 1000
    ");

    po($asins);
    exit;
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
