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
use App\Models\Universal_textile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Services\AWS_Nitshop\Index;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Models\Admin\ErrorReporting;
use App\Models\Catalog\ExchangeRate;
use App\Services\SP_API\API\Catalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;
use App\Models\order\OrderUpdateDetail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TestController;
use App\Services\Inventory\ReportWeekly;
use Spatie\Permission\Models\Permission;
use phpDocumentor\Reflection\Types\Null_;
use SellingPartnerApi\Api\ProductPricingApi;
use App\Jobs\Seller\Seller_catalog_import_job;
use App\Models\ProcessManagement;
use Symfony\Component\Validator\Constraints\File;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;
use App\Services\AWS_Business_API\Auth\AWS_Business;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;
use Illuminate\Validation\Rules\Exists;

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

Route::get('slack', function () {
    $checking = ProcessManagement::get();
    po($checking);
    exit;
    $slackMessage = "testing of slack";
    Log::info($slackMessage);
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

        $asin_source_exists = [];
        $asin_source_not_exists = [];

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

Route::get('order/item', function () {

    $order_id = '403-6898279-3539565';
});

Route::get('order/catalog', function () {

    $order_item_details = DB::connection('order')->select("SELECT seller_identifier, asin, country from orderitemdetails
where status = 0 ");
    $count = 0;
    $batch = 0;
    $asinList = [];
    foreach ($order_item_details as $key => $value) {
        $asin = $value->asin;
        $check = DB::connection('catalog')->select("SELECT asin from catalog where asin = '$asin'");
        // $check = [];
        if (!array_key_exists('0', $check)) {
            // $asinList[$count]->asin = $asin;
            $count++;
            $batch++;
            $data[] = $value;
        }

        //$type = 1 for seller, 2 for Order, 3 for inventory
        if ($count == 10) {
            $count = 0;
            $type = 2;
            $catalog = new Catalog();
            $catalog->index($data, NULL, $type, $batch);
            Log::alert('10 asin imported');
            $data = [];
            // exit;
        }
    }
});

// use ConfigTrait;

Route::get('test/url', function () {

    $feed_id = '129877019312';
    $seller_id = '6';

    $url  = (new FeedOrderDetailsApp360())->getFeedStatus($feed_id, $seller_id);
    $data = file_get_contents($url);

    $data_json = json_decode(json_encode(simplexml_load_string($data)), true);

    $report = $data_json['Message']['ProcessingReport'];
    $success_message = $report['ProcessingSummary']['MessagesSuccessful'];

    if ($success_message == 1) {

        echo $success_message;
    } else {
        po($report['Result']['ResultDescription']);
    }
});

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');
Auth::routes();
Route::get('login', 'Admin\HomeController@dashboard')->name('login');
Route::get('home', 'Admin\HomeController@dashboard')->name('home');
Route::resource('/tests', 'TestController');
Route::get('test/seller', 'TestController@SellerTest');
Route::get('/asin/{asin}/{code}', 'TestController@getASIN');


include_route_files(__DIR__ . '/pms/');
