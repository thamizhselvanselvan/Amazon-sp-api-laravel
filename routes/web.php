<?php

use Carbon\Carbon;
use League\Csv\Reader;
use App\Jobs\TestQueueFail;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Seller\Seller_catalog_import_job;
use App\Services\AWS_Business_API\Search_Product_API\Search_Product;


// use ConfigTrait;


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
Route::get('bb', function () {
    $count = [];
    for ($priority = 1; $priority < 4; $priority++) {

        $tableName = "product_aa_custom_p" . $priority . "_us_seller_details";
        $count['p' . $priority] = DB::connection('buybox')->table($tableName)->get()->count('asin');
    }
    po($count);
    exit;
});
Route::get('cliqnshop', function () {
    $response =   Http::get('http://amazon-sp-api-laravel.app/api/product', [
        'search' => 'leather ball',
        'siteId' => '1.',
        'source' => 'in'
    ]);
    Log::alert($response);
});

Route::get('product/{key}', function ($key) {
    $searchKey = $key;
    $siteId = '4.';
    $source = 'uae';

    $ApiCall = new Search_Product();
    $result = $ApiCall->SearchProductByKey($searchKey, $siteId, $source);
    po($result);
});



Route::get('kyc', function () {
    $b2c_booking = DB::connection('b2cship')->select("SELECT TOP 1 AWBNO, CreatedDate
    FROM Packet ORDER BY CreatedDate DESC");

    po($b2c_booking);
    $kyc_received = DB::connection('b2cship')->select("SELECT TOP 1 AWBNO, CreatedDate
    FROM Packet WHERE IsKYC ='true' ORDER BY CreatedDate DESC");
    po($kyc_received);
    exit;
    $kyc_received_date = Carbon::parse($kyc_received[0]->CreatedDate);
    $dayName = $kyc_received_date->dayName;

    $getTime = Carbon::parse($kyc_received[0]->CreatedDate);
    $now = Carbon::now();
    $timeDiff = $getTime->diff($now);
    po($timeDiff);

    if ($dayName != 'Sunday' && $timeDiff->h >= 3) {
        echo 'kyc not received ';
        // slack_notification('monitor', 'KYC Received', 'KYC received exceeds 11 hours');
    }
});

Route::get('t', function () {

    exit;

    $amazon_order_id = '402-2654368-9851550';
    $order_item_identifier = '45947324279115';

    $order_details = [
        "orderitemdetails.seller_identifier",
        "orderitemdetails.asin",
        "orderitemdetails.seller_sku",
        "orderitemdetails.title",
        "orderitemdetails.order_item_identifier",
        "orderitemdetails.quantity_ordered",
        "orderitemdetails.item_price",
        "orderitemdetails.item_tax",
        "orderitemdetails.shipping_address",

        "orders.fulfillment_channel",
        "orders.our_seller_identifier",
        "orders.amazon_order_identifier",
        "orders.purchase_date",
        "orders.earliest_delivery_date",
        "orders.buyer_info",
        "orders.order_total",
        "orders.latest_delivery_date",
        "orders.is_business_order",
    ];

    $order_item_details = OrderItemDetails::select($order_details)
        ->join('orders', 'orderitemdetails.amazon_order_identifier', '=', 'orders.amazon_order_identifier')
        ->where('orderitemdetails.amazon_order_identifier', $amazon_order_id)
        ->when($order_item_identifier, function ($query, $role) {
            return $query->where('order_item_identifier', $role);
        })
        ->with(['store_details.mws_region'])
        ->limit(1)
        ->first();

    $order = $order_item_details->latest_delivery_date;

    $or = Carbon::parse($order)->format('Y-m-d');

    dd($or);

    dd($order_item_details);
});


Route::get('channel', function () {
    return view('checkChannel');
});

Route::get('job', function () {
    TestQueueFail::dispatch();
});

Route::get('deleterole', function () {
    $role = Role::findByName('Orders');
    $role->delete();
});

Route::get('rename', function () {
    $currenturl = request()->getSchemeAndHttpHost();
    return $currenturl;
});

Route::get('test-queue-redis', function () {

    $order_item_details = DB::connection('order')->select("SELECT seller_identifier, asin, country from orderitemdetails
where status = 0 ");
    $count = 0;
    $batch = 0;
    $asinList = [];
    foreach ($order_item_details as $key => $value) {
        $asin = $value->asin;
        // $check = DB::connection('catalog')->select("SELECT asin from catalog where asin = '$asin'");
        // $check = [];
        // if (!array_key_exists('0', $check)) {
        $count++;
        // $batch++;
        $data[] = $value;
        // }
        //$type = 1 for seller, 2 for Order, 3 for inventory
        if ($count == 10) {

            if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
                Seller_catalog_import_job::dispatch(
                    [
                        'seller_id' => NULL,
                        'datas' => $data,
                        'type' => 1
                    ]
                )->onConnection('redis')->onQueue('default');
            } else {

                Seller_catalog_import_job::dispatch(
                    [
                        'seller_id' => NULL,
                        'datas' => $data,
                        'type' => 1


                    ]
                );
            }
            // $count = 0;
            // $type = 2;
            // $catalog = new Catalog();
            // $catalog->index($data, NULL, $type, $batch);
            // Log::alert('10 asin imported');
            // $data = [];
        }
    }

    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
    } else {
    }
});

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');
Auth::routes();
Route::get('login', 'Admin\HomeController@dashboard')->name('login');
Route::get('home', 'Admin\HomeController@dashboard')->name('home');
Route::resource('/tests', 'TestController');
Route::get('test/seller', 'TestController@SellerTest');
Route::get('/asin/{asin}/{code}', 'TestController@getASIN');

Route::get('testarray', function () {

    $test = [
        'inventory' => ['procurement_price', 'inwared_at'],
        'shipment_inward_details' => ['procurement_price', 'inwared_at'],
        'shipments_inward' => ['inwared_at']
    ];

    dd($test);
});
// include_route_files(__DIR__ . '/pms/');
