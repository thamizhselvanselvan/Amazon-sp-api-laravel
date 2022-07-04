<?php

use App\Services\AWS_Business_API\Auth\AWS_Business;
use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\User;
use League\Csv\Reader;
use App\Events\testEvent;
use AWS\CRT\HTTP\Request;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Dflydev\DotAccessData\Data;
use SellingPartnerApi\Endpoint;
use App\Models\Inventory\Shelve;
use App\Models\Universal_textile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use App\Services\SP_API\API\Catalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use App\Services\Inventory\ReportWeekly;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TestController;
use SellingPartnerApi\Api\ProductPricingApi;
use App\Jobs\Seller\Seller_catalog_import_job;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
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

Route::get('pdf', function (ReportWeekly $report_weekly) {

  $host       = "na.business-api.amazon.com";
  $accessKey  = 'AKIARVGPJZCJHLW5MH63';
  $secretKey  = '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';
  $region     = "us-east-1";
  $service    = "execute-api";
  $requestUrl = "https://na.business-api.amazon.com/products/2020-08-26/products/B081G4G8N8?productRegion=US&locale=es_US";
  $uri        = 'products/2020-08-26/products/B081G4G8N8';
  $httpRequestMethod = 'GET';
  $data       = '';

  $sign = new AWS_Business;
  $headers = $sign->sign($host, $uri, $requestUrl,
              $accessKey, $secretKey, $region, $service,
              $httpRequestMethod, $data);

  apiCall($headers);

  exit;

  $data = '';
  $host               = "na.business-api.amazon.com";
  $accessKey          = "AKIARVGPJZCJHLW5MH63";
  $secretKey          = "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";
  $region             = "us-east-1";
  $service            = "execute-api";
  $requestUrl         = "https://na.business-api.amazon.com/products/2020-08-26/products/B081G4G8N8?productRegion=US&locale=es_US";
  $uri                = 'products/2020-08-26/products';
  $httpRequestMethod  = 'GET';

  $headers = calcualteAwsSignatureAndReturnHeaders($host, $uri, $requestUrl,
              $accessKey, $secretKey, $region, $service,
              $httpRequestMethod, $data);

  apiCall($headers);

  exit;

    // $requestUrl = "https://na.business-api.amazon.com";
    // $httpRequestMethod = "GET";
    // $headers = calcualteAwsSignatureAndReturnHeaders();
    $data = '';

    $host               = "na.business-api.amazon.com";
    // $accessKey          = ACCESS_KEY;
    // $secretKey          = SECRET_KEY;
    $accessKey          = "AKIARVGPJZCJHLW5MH63";
    $secretKey          = "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";
    $region             = "us-east-1";
    $service            = "execute-api";
    $requestUrl         = "https://na.business-api.amazon.com/products/2020-08-26/products/B081G4G8N8?productRegion=US&locale=es_US";
                          //?productRegion=US&locale=es_US
                          //productRegion=US&locale=es_US
    $uri                = 'products/2020-08-26/products/B081G4G8N8';
    $httpRequestMethod  = 'GET';

    $headers = calcualteAwsSignatureAndReturnHeaders($host, $uri, $requestUrl,
                $accessKey, $secretKey, $region, $service,
                $httpRequestMethod, $data);

    $call = callToAPI($requestUrl, $httpRequestMethod, $headers, $data, $debug=TRUE);
    dd($headers, $call);
    exit;

     $host = "na.business-api.amazon.com";
     $uri = "products/2020-08-26/products/B081G4G8N8";
     $requestUrl = "https://na.business-api.amazon.com";
     $accessKey = "AKIARVGPJZCJHLW5MH63";
     $secretKey = "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";
     $region = "us-east-1";
     $service = "execute-api";
     $httpRequestMethod = "";
     $data = "";

     $headers = calcualteAwsSignatureAndReturnHeaders($host, $uri, $requestUrl,
     $accessKey, $secretKey, $region, $service,
     $httpRequestMethod, $data, $debug = TRUE);


     $result = callToAPI($requestUrl, $httpRequestMethod, $headers, $data, TRUE);


     exit;
  $aws = new AWS_Business;

  dd($aws->signTest());

   exit;



  dd($report_weekly->OpeningShipmentCount());

    dd(User::get());

    exit;

    $url = 'https://amazon-sp-api-laravel.test/admin/rolespermissions';
    $file_path = 'product/label.pdf';

    if (!Storage::exists($file_path)) {
        Storage::put($file_path, '');
    }

    $exportToPdf = Storage::path($file_path);
    Browsershot::url($url)
        ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
        ->showBackground()
        ->savePdf($exportToPdf);

    return Storage::download($exportToPdf);
});

Route::get('command', function () {

    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

        Log::warning("Export asin command executed local !");
        $base_path = base_path();
        $command = "cd $base_path && php artisan pms:seller-order-item-import > /dev/null &";
        exec($command);
    } else {

        Artisan::call('pms:seller-order-item-import ');
    }
});


Route::get('test-queue-redis', function () {

    $order_item_details = DB::connection('order')->select("SELECT seller_identifier, asin, country from orderitemdetails where status = 0 ");
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

Route::get('order/item', function (){

    $order_id = '403-6898279-3539565';

});

Route::get('order/catalog', function () {

    $order_item_details = DB::connection('order')->select("SELECT seller_identifier, asin, country from orderitemdetails where status = 0 ");
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

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');
Auth::routes();
Route::get('login', 'Admin\HomeController@dashboard')->name('login');
Route::get('home', 'Admin\HomeController@dashboard')->name('home');
Route::resource('/tests', 'TestController');
Route::get('test/seller', 'TestController@SellerTest');
Route::get('/asin/{asin}/{code}', 'TestController@getASIN');

include_route_files(__DIR__ . '/pms/');
