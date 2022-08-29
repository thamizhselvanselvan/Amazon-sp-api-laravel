<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\User;
use League\Csv\Reader;
use App\Events\testEvent;
use AWS\CRT\HTTP\Request;
use App\Events\checkEvent;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use App\Jobs\TestQueueFail;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Dflydev\DotAccessData\Data;
use SellingPartnerApi\Endpoint;
use App\Models\Inventory\Shelve;
use App\Models\Inventory\Country;
use App\Models\Universal_textile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Services\SP_API\API\Catalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TestController;
use App\Services\Inventory\ReportWeekly;
use Spatie\Permission\Models\Permission;
use SellingPartnerApi\Api\ProductPricingApi;
use App\Jobs\Seller\Seller_catalog_import_job;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;
use App\Services\AWS_Business_API\Auth\AWS_Business;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
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
// use ConfigTrait;

// route::get('newcatalog/{asin}', function($asin){


//     $host = config('database.connections.catalog.host');
//         $dbname = config('database.connections.catalog.database');
//         $port = config('database.connections.catalog.port');
//         $username = config('database.connections.catalog.username');
//         $password = config('database.connections.catalog.password');

//         if (!R::testConnection('catalog', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
//             R::addDatabase('catalog', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
//             R::selectDatabase('catalog');
//         }

//     // $token = 'Atzr|IwEBIJbccmvWhc6q6XrigE6ja7nyYj962XdxoK8AHhgYvfi-WKo3MsrbTSLWFo79My_xmmT48DSVh2e_6w8nxgaeza9XZ9HtNnk7l4Rl_nWhhO6xzEdfIfU7Ev4hktjvU8CjMvYnRn_Cw5JveEqZSggp961Sg7CoBEDpwXZbAE3SYXSdeNxfP2Nu84y2ZzlsP3CNZqcTvXMWflLk1qqY6ittwlGAXpL0BwGxPCBRmjbXOy5xsZqwCPAQhW6l9AJtLPhwOlSSDjcxxvCTH9-LEPSWHLRP1wV3fRgosOlCsQgmuET0pm5SO7FVJTRWux8h2k5hnnM';
//     $token = 'Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg';
//     $country_code = 'IN';
//     $aws_id = NULL;
   
//     $config = new Configuration([
//         "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
//         "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
//         "lwaRefreshToken" => $token,
//         "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
//         "awsSecretAccessKey" => "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",
//         "endpoint" => Endpoint::NA,  // or another endpoint from lib/Endpoints.php
//         "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
//     ]);
//         $apiInstance = new CatalogItemsV20220401Api($config);
//         // po($apiInstance);
//         // exit;
//         // $marketplace_id = 'A21TJRUUN4KGV';
//         $marketplace_id = 'ATVPDKIKX0DER';
//         // $asin = 'B00000JHQ0';

//         $incdata= ['attributes','dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'];
//         echo"<pre>";
//         try {
//             $result = $apiInstance->getCatalogItem($asin, $marketplace_id,$incdata);
//             // po($result);
//             // exit;
//             $result = json_decode(json_encode($result));
//             // $NewCatalogs = R::dispense('catalogussnew');
//                 foreach($result as $key => $value)
//                 {
//                     if($key == 'attributes')
//                     {
//                         foreach($value as $key1 => $value1)
//                         {
//                             // $key3 [] = $key1;
//                             // $ignore_key =['is_expiration_dated_product', 'generic_keyword','externally_assigned_product_identifier', 'recommended_uses_for_product', 'battery', 'supplier_declared_dg_hz_regulation', 'num_batteries', 'batteries_required', 'product_site_launch_date', 'vendor_return_serial_number_required', 'batteries_included', 'product_expiration_type', 'cpsia_cautionary_statement', 'fc_shelf_life', 'warranty_description'];
//                             // $result = array_diff($key3, $ignore_key);
//                             // $NewCatalogs->$key1 = returnType($value1);
//                             // echo $key.' sub_key_1 -> '.$key1;
                            
//                             // $data = $this->returnType($value1);
//                             // po($data);
//                             // echo "<hr>";
    
                            
//                         }
//                         // po($result);
                        
//                     }
//                     elseif($key == 'summaries')
//                     {
//                         foreach((array)$value[0] as $key2 => $value2)
//                         {
//                             // $key2 = str_replace('marketplaceId', 'marketplace', $key2);
//                             // $NewCatalogs->$key2 = returnType($value2);
                                
//                             // echo $key.' sub_key_2 -> '.$key2;
//                             $queue [] = [
//                                 $key2 => returnType($value2),
//                             ];
//                             // $data = $this->returnType($value2);
//                             // po($data);
//                             // echo "<hr>";
//                         }
//                     }
//                     else{
//                         // $NewCatalogs->$key = returnType($value);
//                         // echo $key;
//                         $queue [] = [
//                             $key => returnType($value),
//                         ];
//                         // $data = $this->returnType($value);
//                         // po($data);
//                         // echo "<hr>";
//                     }
//                 }
//                 foreach($queue as $data){
//                     foreach($data as $key3 => $cat){

//                         echo $key3;
//                         po($cat);
//                         echo '<hr>';
//                     }
//                 }
//             // R::store($NewCatalogs);      
//         } catch (Exception $e) {
//             echo $e;
//             // echo 'Exception when calling CatalogItemsV20220401Api->getCatalogItem: ', $e->getMessage(), PHP_EOL;
//         }
// }

// );

function returnType($type){
    $data = '';
    if(is_object($type)){
        $data = json_encode($type);
    }
    elseif(is_string($type))
    {
        $data = $type;
    }else{
        $data = json_encode($type);
    }
    return $data;
}



Route::get('country', function () {


    Log::channel('slack')->error('Hello world! for app 360');
    exit;

    $path =  public_path('country.json');
    $jsonfile = json_decode(file_get_contents($path), true);
    $countries_list = [];

    foreach ($jsonfile as $jsondata) {
        $countries_list[] = [

            "name" => $jsondata['name'],
            "country_code" => $jsondata['iso3'],
            "code" => $jsondata['iso2'],
            "numeric_code" => $jsondata['numeric_code'],
            "phone_code" => $jsondata['phone_code'],
            "capital" => $jsondata['capital'],
            "currency" => $jsondata['currency'],
            "currency_name" => $jsondata['currency_name'],
            "currency_symbol" => $jsondata['currency_symbol'],
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
    po($countries_list);

    $country_count = Country::count();

    if ($country_count <= 0) {
        Country::insert($countries_list);
    }

    $countries = Country::get();
});
Route::get('TrackingApi', function () {

    // return $awbNo;
    // $url = "https://amazon-sp-api-laravel.app/api/testing?awbNo=US30000002";
    // $awbNo = 'US30000002';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://amazon-sp-api-laravel.app/api/testing/awbNo=US30000002",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>
<AmazonTrackingRequest xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' 
xsi:noNamespaceSchemaLocation='AmazonTrackingRequest.xsd'>
</AmazonTrackingRequest>",
        CURLOPT_HTTPHEADER => array(
            'Content-Type: text/plain',

        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
});

Route::get('channel', function () {
    return view('checkChannel');
});

Route::get('test', function (ReportWeekly $report_weekly) {

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
    $headers = $sign->sign(
        $host,
        $uri,
        $requestUrl,
        $accessKey,
        $secretKey,
        $region,
        $service,
        $httpRequestMethod,
        $data
    );

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

    $headers = calcualteAwsSignatureAndReturnHeaders(
        $host,
        $uri,
        $requestUrl,
        $accessKey,
        $secretKey,
        $region,
        $service,
        $httpRequestMethod,
        $data
    );

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

    $headers = calcualteAwsSignatureAndReturnHeaders(
        $host,
        $uri,
        $requestUrl,
        $accessKey,
        $secretKey,
        $region,
        $service,
        $httpRequestMethod,
        $data
    );

    $call = callToAPI($requestUrl, $httpRequestMethod, $headers, $data, $debug = TRUE);
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

    $headers = calcualteAwsSignatureAndReturnHeaders(
        $host,
        $uri,
        $requestUrl,
        $accessKey,
        $secretKey,
        $region,
        $service,
        $httpRequestMethod,
        $data,
        $debug = TRUE
    );


    $result = callToAPI($requestUrl, $httpRequestMethod, $headers, $data, TRUE);


    exit;
    $aws = new AWS_Business;

    dd($aws->signTest());

    exit;






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

Route::get('job', function () {
    TestQueueFail::dispatch();
});

Route::get('deleterole', function () {
    $role = Role::findByName('Orders');
    $role->delete();
});

Route::get('rename', function () {
    $currenturl =  request()->getSchemeAndHttpHost();
    return $currenturl;
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

Route::get('order/item', function () {

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
