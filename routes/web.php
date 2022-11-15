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
use App\Services\Zoho\ZohoApi;
use App\Models\FileManagement;
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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TestController;
use App\Services\Inventory\ReportWeekly;
use Spatie\Permission\Models\Permission;
use phpDocumentor\Reflection\Types\Null_;
use SellingPartnerApi\Api\ProductPricingApi;
use App\Jobs\Seller\Seller_catalog_import_job;
use Symfony\Component\Validator\Constraints\File;
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

Route::get('zoho_update', function () {

    //$idToWork = 377125000000428001;
    //$idToWork = 377125000000428035;

    $zoho = new ZohoApi;
    // dd($zoho->getLead($idToWork));
    // dd($zoho->search('403-4468830-9728365', '28528755520011'));
    dd($zoho->search('405-3078074-2714731', '14324755670403'));

    exit;
    $test = json_decode('{"data":[{"code":"SUCCESS","details":{"Modified_Time":"2022-11-11T18:13:51+05:30","Modified_By":{"name":"Mosh","id":"1929333000000097003"},"Created_Time":"2022-11-11T18:13:51+05:30","id":"1929333000099290066","Created_By":{"name":"Mosh","id":"1929333000000097003"}},"message":"record added","status":"success"}]}', true);

    if (array_key_exists('data', $test) && array_key_exists(0, $test['data']) && array_key_exists('code', $test['data'][0])) {
        //   /  dd($test['data'][0]['details']['id']);
    }

    dd($test);

    exit;

    // $zoho = new ZohoApi;
    // dd($zoho->getAccessToken());


    //
    // $robin = User::create([
    //     'name' => 'Robin Singh',
    //     'email' => 'cliqnshop@app360.io',
    //     'password' => Hash::make(123456),
    // ]);
    // $invoice = Role::create(['name' => 'Cliqnshop']);
    // $invoice_permission = Permission::create(['name' => 'Cliqnshop']);
    // $invoice->givePermissionTo($invoice_permission);
    // $robin->assignRole('Cliqnshop');


    // exit;

    $ZohoOrder = new ZohoOrder;

    dd($ZohoOrder->index());
});






Route::get('import', function () {

    $command_info = json_encode([
        'fm_id' => 12,
        'user_id' => 13
    ]);

    $test = ($command_info);
    po(json_decode($command_info));
    exit;
    $auth_count = 0;
    $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', 'US')->get()->toArray();
    $id = $mws_regions[0]['aws_verified'][$auth_count]['id'];

    $aws_token = Aws_credential::where('id', $id)->get()->pluck('auth_code')->toArray();
    $token = $aws_token[0] ?? '';
    po(($token));
});

$delist_asins;
Route::get('wherein', function () {
    $asins = ['B0006KQH6A', 'B0006G5MHO'];

    $data =  PricingIn::select('destination.asin as asin', 'cat.product_types', 'cat.images', 'cat.dimensions', 'pricing_ins.in_price', 'pricing_ins.ind_to_uae', 'pricing_ins.ind_to_sg', 'pricing_ins.updated_at', 'pricing_ins.weight')
        ->join("asin_destination_ins as destination", 'pricing_ins.asin', '=', 'destination.asin')
        ->join("catalognewins as cat", 'pricing_ins.asin', '=', 'cat.asin')
        ->where('destination.priority', 1)
        ->whereIn('destination.asin', $asins)
        ->get()->toArray();
    $di = [];
    if (count($data) > 0) {

        foreach ($data as $key => $record) {
            foreach ($record as $key2 => $value) {

                if ($key2 == 'images') {
                    $images = json_decode($value);
                    $image = isset($images[0]->images) ? $images[0]->images : 'NA';
                    $di[$key]['image1'] = isset($image[0]->link) ? $image[0]->link : 'NA';
                    $di[$key]['image2'] = isset($image[1]->link) ? $image[1]->link : 'NA';
                }
                if ($key2 == 'product_types') {
                    $product_types = json_decode($value);
                    $di[$key]['product_types'] = isset($product_types[0]->productType) ? $product_types[0]->productType : 'NA';
                }

                if ($key2 == 'dimensions') {

                    $dimension = json_decode($value);
                    $package = isset($dimension[0]->package) ? $dimension[0]->package : 'NA';

                    $di[$key]['height'] = isset($package->height->value) ? $package->height->value : 'NA';
                    $di[$key]['length'] = isset($package->length->value) ? $package->length->value : 'NA';
                    $di[$key]['width'] = isset($package->width->value) ? $package->width->value : 'NA';
                    $di[$key]['unit'] = isset($package->width->unit) ? $package->width->unit : 'NA';
                    // $di[$key]['weight'] = isset($package->weight->value) ? $package->weight->value : 'NA';
                    // $di[$key]['weight_unit'] = isset($package->weight->unit) ? $package->weight->unit : 'NA';
                    //$di[$key]['updated_at'] = isset($record['updated_at']) ? date("d-m-Y h:i:s", strtotime($record['updated_at'])) : 'NA';
                }


                if ($key2 != 'dimensions' && $key2 != 'updated_at' && $key2 != 'images' && $key2 != 'product_types') {

                    $di[$key][$key2] = $value ?? 'NA';
                }
            }
        }
    }
    po($di);
    exit;

    $dbname = config('database.connections.catalog.database');
    $destination_table = "asin_destination_uss";
    $buybox_table = "bb_product_aa_custom_p2_us_offers";

    $table = table_model_create(country_code: 'in', model: 'Asin_destination', table_name: 'asin_destination_');

    $unavailable_catalog = $table->select("asin_destination_ins.asin")
        ->join("catalognewins as catalog", 'catalog.asin', '=', 'asin_destination_ins.asin')
        ->groupBy('asin_destination_ins.priority');

    po(($unavailable_catalog));
    exit;

    $asins = [];
    $delist_asin_count = [];
    $gross = 0;
    $count = [];
    for ($priority = 1; $priority <= 3; $priority++) {
        $gross = 0;
        $data = $table->select('id', 'asin')->where('priority', $priority)->chunkbyid(5000, function ($result) use ($priority, $gross) {
            foreach ($result as $delist_asin) {
                $asins[] = "'$delist_asin->asin'";
            }
            $asin = implode(',', $asins);
            $buybox_table = "bb_product_aa_custom_p${priority}_in_offers";
            $delist_asin_count[] = DB::connection('buybox')->select("SELECT count(asin)as asin_delist
            FROM ${buybox_table} 
            WHERE asin IN ($asin)
            and delist = 1
            group by priority
            ");
            foreach ($delist_asin_count as $asin_delist) {

                if (isset($asin_delist[0])) {
                    // po($gross = &$gross + $asin_delist[0]->asin_delist);
                    // po($asin_delist[0]);
                }
            }
            po($delist_asin_count);
        });
    }
});

Route::get('data', function () {

    $tgh = ExchangeRate::select(
        'source_destination',
        DB::raw("group_concat(`base_weight`) as base_weight, 
    group_concat(`base_shipping_charge`) as base_shipping_charge,
    group_concat(packaging) as packaging,
    group_concat(seller_commission) as seller_commission,
    group_concat(duty_rate) as duty_rate,
    group_concat(sp_commission) as sp_commission,
    group_concat(excerise_rate) as excerise_rate,
    group_concat(amazon_commission) as amazon_commission
    ")
    )->groupBy('source_destination')->get()->toArray();

    po($tgh);
    exit;

    $value = [];
    $datas = [
        'B00014DZL6',
        'B00014E9W0',
        'B01DBQIIGC',
        'B01DGIKD1I',
        'B01DNYGMZ6',
        'B01DPEGT4S',
    ];
    foreach ($datas as $data) {
        $value[] = $data;
    }

    po($value);
});

// route::get(
//     'newcatalog',
//     function () {


//         $host = config('database.connections.catalog.host');
//         $dbname = config('database.connections.catalog.database');
//         $port = config('database.connections.catalog.port');
//         $username = config('database.connections.catalog.username');
//         $password = config('database.connections.catalog.password');

//         if (!R::testConnection('catalog', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
//             R::addDatabase('catalog', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
//             R::selectDatabase('catalog');
//         }

//         // $token = 'Atzr|IwEBIJbccmvWhc6q6XrigE6ja7nyYj962XdxoK8AHhgYvfi-WKo3MsrbTSLWFo79My_xmmT48DSVh2e_6w8nxgaeza9XZ9HtNnk7l4Rl_nWhhO6xzEdfIfU7Ev4hktjvU8CjMvYnRn_Cw5JveEqZSggp961Sg7CoBEDpwXZbAE3SYXSdeNxfP2Nu84y2ZzlsP3CNZqcTvXMWflLk1qqY6ittwlGAXpL0BwGxPCBRmjbXOy5xsZqwCPAQhW6l9AJtLPhwOlSSDjcxxvCTH9-LEPSWHLRP1wV3fRgosOlCsQgmuET0pm5SO7FVJTRWux8h2k5hnnM';
//         // $token = 'Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg';
//         $country_code = 'US';
//         $aws_id = NULL;
//         // $country_code = 'IN';
//         $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->toArray();
//         foreach ($mws_regions[0]['aws_verified'] as $mws_region) {

//             $token = $mws_region['auth_code'];
//             po($token);
//             $config = new Configuration([
//                 "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
//                 "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
//                 "lwaRefreshToken" => $token,
//                 "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
//                 "awsSecretAccessKey" => "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",
//                 "endpoint" => Endpoint::NA,  // or another endpoint from lib/Endpoints.php
//                 "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
//             ]);
//             $apiInstance = new CatalogItemsV20220401Api($config);
//             // po($apiInstance);
//             // exit;
//             // $marketplace_id = 'A21TJRUUN4KGV';
//             $marketplace_id = ['ATVPDKIKX0DER'];
//             // $asin = 'B00000JHQ0';
//             $asins = [
//                 // 'B08DR3YCQ6',
//                 'B085BLCJBT',
//                 'B000WA6KFK',
//                 'B000WH10SW',
//                 'B000WNAP6O',
//                 'B000XAL2F4',
//                 'B000ZHJS0G',
//                 'B0B7KLY36F',
//                 'B0B7QZZLTH',
//                 'B0B7TT98RK',
//                 'B0B7XH7SQM',
//                 'B0B7XY7XKX',
//                 'B0B811JZQ9',
//                 'B0B8125NJY',
//                 'B0B81714JF',
//                 'B0B8CDYR6P',
//                 'B0B8DCP9P2',
//                 'B0B8GPYWZ1',
//                 'B0B8MMH4K7',
//                 'B0B994XM28'
//             ];
//             $identifiers = $asins;
//             $identifiers_type = 'ASIN';
//             $page_size = 20;
//             $locale = null;
//             $seller_id_temp = null;
//             $keywords = null;
//             $brand_names = null;
//             $classification_ids = null;
//             $page_token = null;
//             $keywords_locale = null;

//             $includedData = ['attributes', 'dimensions', 'identifiers', 'relationships', 'salesRanks', 'images', 'productTypes', 'summaries'];
//             echo "<pre>";
//             try {
//                 $data = [];
//                 $miss_asin = [];
//                 $result = $apiInstance->searchCatalogItems(
//                     $marketplace_id,
//                     $identifiers,
//                     $identifiers_type,
//                     $includedData,
//                     $locale,
//                     $seller_id_temp,
//                     $keywords,
//                     $brand_names,
//                     $classification_ids,
//                     $page_size,
//                     $page_token,
//                     $keywords_locale,
//                 );
//                 $result = json_decode(json_encode($result));
//                 // po($result);
//                 // exit;
//                 foreach ($result->items as $key => $value) {
//                     $diff_array[] = $value->asin;
//                     // po($diff_array);
//                     // exit;
//                     foreach ($value as $key1 => $value1) {
//                         if ($key1 == 'summaries') {
//                             foreach ($value1[0] as $key2 => $value2) {
//                                 $data[$key][$key2] = returnType($value2);
//                                 // echo $key2;
//                                 // echo '<br>';
//                                 // print_r($value2);
//                                 // echo '<hr>';
//                             }
//                         } elseif ($key1 == 'dimensions') {
//                             if (array_key_exists('package', (array)$value1[0])) {
//                                 foreach ($value1[0]->package as $key3 => $value3) {
//                                     // echo $key3;
//                                     // echo '<br>';
//                                     // print_r($value3);
//                                     // echo '<hr>';
//                                     $data[$key][$key3] = $value3->value;
//                                     if ($key3 == 'width' || $key3 == 'lenght' || $key3 == 'height') {
//                                         $data[$key]['unit'] = $value3->unit;
//                                     }
//                                     if ($key3 == 'weight') {

//                                         $data[$key]['weight_unit'] = $value3->unit;
//                                     }
//                                 }
//                             }
//                         } else {
//                             $data[$key][$key1] = returnType($value1);
//                             // echo $key1;
//                             // echo '<br>';
//                             // print_r($value1);
//                             // echo '<hr>';
//                         }
//                     }
//                 }
//                 po($data);
//             } catch (Exception $e) {
//                 $error_record = [
//                     'queue_type' => 'Catalog',
//                     'source' => $country_code,
//                     'identifier_type' => 'ASIN',
//                 ];

//                 ErrorReporting::insert($error_record);
//                 // echo $e->getMessage(), PHP_EOL;

//                 print_r($e->getMessage());

//                 // echo 'Exception when calling CatalogItemsV20220401Api->getCatalogItem: ', $e->getMessage(), PHP_EOL;
//             }
//         }
//     }

// );

// function returnType($type)
// {
//     $data = '';
//     if (is_object($type)) {
//         $data = json_encode($type);
//     } elseif (is_string($type)) {
//         $data = $type;
//     } else {
//         $data = json_encode($type);
//     }
//     return $data;
// }



Route::get('country', function () {

    Log::channel('slack')->error('Hello world! for app 360');

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

// Route::get('test', function (ReportWeekly $report_weekly) {

//     $host = "na.business-api.amazon.com";
//     $accessKey = 'AKIARVGPJZCJHLW5MH63';
//     $secretKey = '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';
//     $region = "us-east-1";
//     $service = "execute-api";
//     $requestUrl =
//         "https://na.business-api.amazon.com/products/2020-08-26/products/B081G4G8N8?productRegion=US&locale=es_US";
//     $uri = 'products/2020-08-26/products/B081G4G8N8';
//     $httpRequestMethod = 'GET';
//     $data = '';

//     $sign = new AWS_Business;
//     $headers = $sign->sign(
//         $host,
//         $uri,
//         $requestUrl,
//         $accessKey,
//         $secretKey,
//         $region,
//         $service,
//         $httpRequestMethod,
//         $data
//     );

//     apiCall($headers);

//     exit;

//     $data = '';
//     $host = "na.business-api.amazon.com";
//     $accessKey = "AKIARVGPJZCJHLW5MH63";
//     $secretKey = "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";
//     $region = "us-east-1";
//     $service = "execute-api";
//     $requestUrl =
//         "https://na.business-api.amazon.com/products/2020-08-26/products/B081G4G8N8?productRegion=US&locale=es_US";
//     $uri = 'products/2020-08-26/products';
//     $httpRequestMethod = 'GET';

//     $headers = calcualteAwsSignatureAndReturnHeaders(
//         $host,
//         $uri,
//         $requestUrl,
//         $accessKey,
//         $secretKey,
//         $region,
//         $service,
//         $httpRequestMethod,
//         $data
//     );

//     apiCall($headers);

//     exit;


//     $data = '';

//     $host = "na.business-api.amazon.com";

//     $accessKey = "AKIARVGPJZCJHLW5MH63";
//     $secretKey = "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";
//     $region = "us-east-1";
//     $service = "execute-api";
//     $requestUrl =
//         "https://na.business-api.amazon.com/products/2020-08-26/products/B081G4G8N8?productRegion=US&locale=es_US";

//     $uri = 'products/2020-08-26/products/B081G4G8N8';
//     $httpRequestMethod = 'GET';

//     $headers = calcualteAwsSignatureAndReturnHeaders(
//         $host,
//         $uri,
//         $requestUrl,
//         $accessKey,
//         $secretKey,
//         $region,
//         $service,
//         $httpRequestMethod,
//         $data
//     );

//     $call = callToAPI($requestUrl, $httpRequestMethod, $headers, $data, $debug = TRUE);
//     dd($headers, $call);
//     exit;

//     $host = "na.business-api.amazon.com";
//     $uri = "products/2020-08-26/products/B081G4G8N8";
//     $requestUrl = "https://na.business-api.amazon.com";
//     $accessKey = "AKIARVGPJZCJHLW5MH63";
//     $secretKey = "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";
//     $region = "us-east-1";
//     $service = "execute-api";
//     $httpRequestMethod = "";
//     $data = "";

//     $headers = calcualteAwsSignatureAndReturnHeaders(
//         $host,
//         $uri,
//         $requestUrl,
//         $accessKey,
//         $secretKey,
//         $region,
//         $service,
//         $httpRequestMethod,
//         $data,
//         $debug = TRUE
//     );


//     $result = callToAPI($requestUrl, $httpRequestMethod, $headers, $data, TRUE);


//     exit;
//     $aws = new AWS_Business;

//     dd($aws->signTest());

//     exit;






//     exit;

//     $url = 'https://amazon-sp-api-laravel.test/admin/rolespermissions';
//     $file_path = 'product/label.pdf';

//     if (!Storage::exists($file_path)) {
//         Storage::put($file_path, '');
//     }

//     $exportToPdf = Storage::path($file_path);
//     Browsershot::url($url)
//         ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
//         ->showBackground()
//         ->savePdf($exportToPdf);

//     return Storage::download($exportToPdf);
// });

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

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');
Auth::routes();
Route::get('login', 'Admin\HomeController@dashboard')->name('login');
Route::get('home', 'Admin\HomeController@dashboard')->name('home');
Route::resource('/tests', 'TestController');
Route::get('test/seller', 'TestController@SellerTest');
Route::get('/asin/{asin}/{code}', 'TestController@getASIN');

include_route_files(__DIR__ . '/pms/');
