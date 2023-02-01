<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Label;
use League\Csv\Reader;
use App\Events\testEvent;
use App\Events\checkEvent;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use App\Jobs\TestQueueFail;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use App\Models\Aws_credential;
use App\Models\FileManagement;
use App\Services\Zoho\ZohoApi;
use App\Models\GoogleTranslate;
use Dflydev\DotAccessData\Data;
use SellingPartnerApi\Endpoint;
use App\Models\Inventory\Shelve;
use App\Services\Zoho\ZohoOrder;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Inventory\Country;
use App\Models\ProcessManagement;
use App\Models\Universal_textile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Services\AWS_Nitshop\Index;
use function Clue\StreamFilter\fun;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Models\Admin\ErrorReporting;
use App\Models\Catalog\ExchangeRate;
use App\Services\SP_API\API\Catalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;
use App\Models\order\OrderUpdateDetail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Exists;
use ParagonIE\Sodium\Core\Curve25519\H;
use App\Http\Controllers\TestController;
use App\Services\Inventory\ReportWeekly;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use phpDocumentor\Reflection\Types\Null_;
use SellingPartnerApi\Api\ProductPricingApi;
use App\Jobs\Seller\Seller_catalog_import_job;
use App\Models\Buybox_stores\Product;
use Google\Cloud\Translate\V2\TranslateClient;
use Symfony\Component\Validator\Constraints\File;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;
use App\Services\AWS_Business_API\Auth\AWS_Business;

use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;
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

    // -- group_concat(PPO.is_fulfilled_by_amazon) as is_fulfilled_by_amazon,
    //                         -- group_concat(PPO.feedback_count) as feedback_count

    $destination_model = table_model_create(country_code: 'IN', model: 'Asin_destination', table_name: 'asin_destination_');
    $product_seller_details = "bb_product_aa_custom_p1_in_seller_details";
    $product_lp = "bb_product_aa_custom_p1_in_offers";

    $data = $destination_model->select(['asin', 'user_id'])
        ->where('price_status', 0)->where('priority', '1')
        ->limit(1000)
        ->get()->toArray();

    $asin = [];
    foreach ($data as $value) {
        $a = $value['asin'];
        $asin[] = "'$a'";
    }
    $asin = implode(',', $asin);
    $asin_price = DB::connection('buybox')
        ->select("SELECT PPO.asin, LP.available,LP.is_sold_by_amazon,LP.buybox_listingprice_amount, LP.updated_at as updated_at,
                            GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                            group_concat(PPO.listingprice_amount) as listingprice_amount,
                            group_concat(PPO.seller_store_id) as seller_store_id
                            FROM 
                                $product_seller_details as PPO
                                    JOIN
                                 $product_lp as LP 
                            ON 
                                PPO.asin = LP.asin
                            WHERE
                                 PPO.asin IN ($asin)
                            GROUP BY 
                                PPO.asin
                                
                        ");
    // po($asin_price);
    // exit;

    $lowest_key = [];
    $highest_key = [];
    $bb_winner_id = '';
    $seller_lowest_price = [];
    $seller_highest_price = [];
    $seller_store_id = [];
    $asin = [];
    $store_id = Mws_region::with('aws_verified1')->where('region_code', 'IN')
        ->get()->toArray();

    foreach ($store_id[0]['aws_verified1'] as $merchant_id) {
        $seller_store_id[] = $merchant_id['seller_id'];
        $bb_winner_price = '';
        foreach ($asin_price as $key => $bb_asin) {

            $bb_store_key = [
                'cyclic' => '5',
                'is_bb_won' => '0',
                'bb_winner_id' => '',
                'bb_winner_price' => '',
                'lowest_seller_id' => '',
                'lowest_seller_price' => '',
                'highest_seller_id' => '',
                'highest_seller_price' => ''
            ];
            $asin[] = $bb_asin->asin;
            $is_bb_winners = explode(',', $bb_asin->is_buybox_winner);
            $listingPrice_amount = explode(',', $bb_asin->listingprice_amount);
            $seller_store_id = explode(',', $bb_asin->seller_store_id);
            // po($bb_asin);
            // po($listingPrice_amount);
            $bb_winner_price = '';
            foreach ($is_bb_winners as $key2 => $bb_won) {
                if ($bb_won == 1) {

                    $bb_winner_price = $listingPrice_amount[$key2];
                    $bb_store_key['is_bb_won'] = $seller_store_id[$key2] == $merchant_id['seller_id'] ? 1 : 2;
                    $bb_store_key['bb_winner_id'] = $seller_store_id[$key2];
                    $bb_store_key['bb_winner_price'] = $bb_winner_price;
                }
            }
            foreach ($is_bb_winners as $key1 => $is_bb_winner) {

                if ($bb_winner_price != '') {

                    if ($listingPrice_amount[$key1] > $bb_winner_price) {
                        $seller_highest_price[] = $listingPrice_amount[$key1];
                        $highest_key[] = $key1;
                    }

                    if ($listingPrice_amount[$key1] < $bb_winner_price) {
                        $seller_lowest_price[] = $listingPrice_amount[$key1];
                        $lowest_key[] = $key1;
                    }
                }
            }

            if ($seller_highest_price != null) {

                $bb_store_key['highest_seller_id'] = $seller_store_id[min($highest_key)];
                $bb_store_key['highest_seller_price'] = min($seller_highest_price);
                $highest_key = [];
                $seller_highest_price = [];
            }
            if ($seller_lowest_price != null) {

                $bb_store_key['lowest_seller_id'] = $seller_store_id[max($lowest_key)];
                $bb_store_key['lowest_seller_price'] = max($seller_lowest_price);
                $lowest_key = [];
                $seller_lowest_price = [];
            }
            po($bb_store_key);
            echo '<hr>';

            // Product::where('store_id', $merchant_id['seller_id'])
            //     ->where('asin', $bb_asin->asin)
            //     ->update($bb_store_key);
            // $bb_store_key = [];
        }
        // po($seller_store_id);
        // po($bb_store_key);
    }
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
// uncomment this one it is commented
// include_route_files(__DIR__ . '/pms/');