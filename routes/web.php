<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\User;
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
use Symfony\Component\Validator\Constraints\File;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;
use App\Services\AWS_Business_API\Auth\AWS_Business;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

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
Route::get('cliqnshop', function () {
    $response =   Http::get('http://amazon-sp-api-laravel.app/api/product', [
        'search' => 'iPhone',
        'siteId' => '4.',
        'source' => 'uae'
    ]);
    // Log::alert($response);
});

Route::get('kyc', function () {
    $kyc_received = DB::connection('b2cship')->select("SELECT TOP 1 AWBNO, CreatedDate
    FROM Packet WHERE IsKYC ='true' ORDER BY CreatedDate DESC");

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
    $results = CSV_Reader('INDIA ALL ASINS.csv');

    $tagger = 0;
    $csv_lists = [];
    $limit = 15000;
    $counter = 1;

    foreach ($results as $result) {

        $csv_lists[$tagger][] = $result['asin1'];

        if ($limit == $counter) {

            $tagger++;

            $counter = 0;
        }

        $counter++;
    }

    foreach ($csv_lists as $csv_list) {

        $sources = DB::connection('catalog')->table('asin_source_ins')->select('asin')->whereIn('asin', $csv_list)->get()->groupBy('asin')->toArray();
        $destinations = DB::connection('catalog')->table('asin_destination_ins')->select('asin', 'priority')->whereIn('asin', $csv_list)->get()->groupBy('asin')->toArray();

        $asin_destination_exists = [];
        $asin_destination_not_exists = [];

        foreach ($csv_list as $asin) {

            if (isset($destinations[$asin])) {

                $asin_destination_exists[] = ['asin' => $asin, "priority" => $destinations[$asin][0]->priority];
            } else {
                $asin_destination_not_exists[] = ['asin' => $asin, 'priority' => 0];
            }
        }

<<<<<<< HEAD
        $asin_source_exists = [];
        $asin_source_not_exists = [];
=======
    // exit; 
>>>>>>> c9cd7e131182ee0d3e86da8da2f04cfb5d64050b

        foreach ($csv_list as $asin) {

            if (isset($sources[$asin])) {
                $asin_source_exists[] = ['asin' => $asin];
            } else {
                $asin_source_not_exists[] = ['asin' => $asin];
            }
        }

        CSV_w('Data/in/destination_exists.csv', $asin_destination_exists, ['asin', 'priority']);
        CSV_w('Data/in/destination_not_exists.csv', $asin_destination_not_exists, ['asin', 'priority']);
        CSV_w('Data/in/source_exists.csv', $asin_source_exists, ['asin']);
        CSV_w('Data/in/source_not_exists.csv', $asin_source_not_exists, ['asin']);
    }
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
