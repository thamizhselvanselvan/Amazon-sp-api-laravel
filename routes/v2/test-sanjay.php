<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Events\EventManager;
use App\Models\Admin\Backup;
use App\Models\Aws_credential;
use App\Services\Zoho\ZohoApi;
use PhpParser\Node\Stmt\Foreach_;
use App\Models\Inventory\Shipment;
use App\Models\ShipNTrack\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\ShipNTrack\Courier\Courier;
use App\Models\Inventory\Shipment_Inward_Details;
use App\Http\Controllers\Inventory\StockController;
use App\Models\ShipNTrack\Courier\StatusManagement;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

Route::get('sanju/test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');
Route::get('sanju/test/controller', 'SanjayTestController@index');

Route::get('sanju/test/images', function () {
    $data = ('[{"marketplaceId":"ATVPDKIKX0DER","images":[{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/71OWSOnk+zL.jpg","height":1000,"width":1000},{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/61Wpkl8oVDL.jpg","height":500,"width":500},{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/61Wpkl8oVDL._SL75_.jpg","height":75,"width":75},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/71--NVjLvhL.jpg","height":1000,"width":1000},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/61XF+tw0r0L.jpg","height":500,"width":500},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/61XF+tw0r0L._SL75_.jpg","height":75,"width":75},{"variant":"PT02","link":"https:\/\/m.media-amazon.com\/images\/I\/715M80emuTL.jpg","height":1000,"width":1000},{"variant":"PT02","link":"https:\/\/m.media-amazon.com\/images\/I\/616EHH0o7YL.jpg","height":500,"width":500},{"variant":"PT02","link":"https:\/\/m.media-amazon.com\/images\/I\/616EHH0o7YL._SL75_.jpg","height":75,"width":75}]}]');
    $imagedata = json_decode($data, true);
    po($imagedata);
    // exit;
    if (isset($imagedata[0]['images'])) {

        foreach ($imagedata[0]['images'] as $counter => $image_data_new) {
            $counter++;

            if (array_key_exists("link", $image_data_new)) {
                $img1["Images${counter}"] = '';
                if ($counter == 1) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                } else if ($counter == 4) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                } else if ($counter == 7) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                } else if ($counter == 10) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                } else if ($counter == 13) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                }
            }
        }
    } else {
        for ($i = 1; $i <= 5; $i++) {
            $img1["Images${i}"] = '';
        }
    }

    po($img1);
});
//generic Keywords fetch
Route::get('sanju/test/generic_key', function () {
    $asin = [
        'TB0721C6JC3',
        'TB071H1VQCY',
        'TB071GZPPQ4',
        'TB071GYSJ1F',
        'TB071GYSJ1F',

    ];
    $headers = [
        'catalognewuss.asin',
        'catalognewuss.brand',
        'catalognewuss.images',
        'catalognewuss.item_name',
        'catalognewuss.browse_classification',
        'catalognewuss.dimensions',
        'catalognewuss.attributes',
        'catalognewuss.color',
        'pricing_uss.usa_to_in_b2c',
        'pricing_uss.us_price',
        'pricing_uss.usa_to_uae',

    ];
    $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');
    $result = $table_name->select($headers)
        ->join('pricing_uss', 'catalognewuss.asin', '=', 'pricing_uss.asin')
        ->whereIn('catalognewuss.asin', $asin)
        ->get()->toArray();

    foreach ($result as $data) {

        if (isset($data['attributes'])) {
            $genric_key = json_decode($data['attributes'], true);


            if (isset($genric_key['generic_keyword']) && !empty($genric_key['generic_keyword'])) {

                $generic_array = $genric_key['generic_keyword'];

                foreach ($generic_array as $key => $val) {

                    $generic_keywords[$data['asin']][] = $val['value'];
                }
            }
        }
    }
    po($generic_keywords);
});

//zajel POC
Route::get('sanju/zajel/tracking', function () {

    $awb = "Z6430506";
    $requestUrl = "https://app.shipsy.in/api/customer/integration/consignment/track?reference_number=$awb";
    $api_key = 'a80517c76ae63a0dc191df8484b24d';

    //with HTTP
    $response = Http::withHeaders([
        'api-key' => $api_key,
    ])->get($requestUrl);

    $reference_number = '';
    $status = '';
    if ($response->successful()) {
        $datas = $response->json();
        po($datas);
        exit;


        $reference_number = ($datas['reference_number']);
        $status = ($datas['status']);
        $events = ($datas['events']);
        foreach ($events as $key => $event) {

            $type['type'] = ($event['type']);
            $hub_name['hub_name'] = ($event['hub_name']);
            $customer_update['customer_update'] = ($event['customer_update']);
            $failure_reason['failure_reason'] = ($event['failure_reason']);
            $responce[] = [
                'type' =>  $type['type'],
                'hub_name' => $hub_name['hub_name'],
                'customer_update' => $customer_update['customer_update'],
                'failure_reason' => $failure_reason['failure_reason'],

            ];
        }
    } else {
        $responce[] = [
            'respnse' => 'Invalid Refrence Number or No Details Found',

        ];
    }
    $data = [
        'status' => $status,
        'reference_number' => $reference_number,
        'responce' => $responce,

    ];
    po($data);

    //with curl
    // $curl = curl_init();
    // $headersFS = array(
    //     'api-key:' . $api_key,
    // );
    // curl_setopt($curl, CURLOPT_URL, $requestUrl . '');
    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    // curl_setopt($curl, CURLOPT_HTTPHEADER, $headersFS);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


    // $server_APIoutput = curl_exec($curl);
    // $JsonResponse = json_decode($server_APIoutput);

    // po($JsonResponse);

    // if (curl_errno($curl)) {
    //     echo 'Error:' . curl_error($curl);
    // }

    // curl_close($curl);
});

Route::get('sanju/cns/deletion/logic', function () {
    $files = Storage::files('Cliqnshop\asin_import');
    $DaysBefore = Carbon::now()->subDays(30);

    foreach ($files as $file) {

        $lastModified = Storage::lastModified($file);
        $lastModifiedTime = Carbon::createFromTimestamp($lastModified);

        if ($lastModifiedTime->lt($DaysBefore)) {
            Storage::delete($file);
        }
        po('deleted');
    }
});

Route::get('sanju/db/backup', function () {

    $databaseName = Config::get('database.connections');

    $ignoreArray = [
        'order_no_prefix',
        'buybox',
        'bbstores',
        'aws',
        'b2cship',
        'mongodb',
        'cliqnshop',
        'buybox_stores',
    ];

    foreach ($databaseName as $key => $table) {
        $connections[] = $key;

        $final_connection = array_filter($connections, function ($item) use ($ignoreArray) {
            return (!in_array($item, $ignoreArray));
        });
    }

    foreach ($final_connection as $key => $value) {

        $db_tables[$value] = Schema::connection($value)->getAllTables();
    }
    $web_table = (array) $db_tables['web'];
    $inventory_table = (array)$db_tables['inventory'];
    $order_table = (array) $db_tables['order'];
    $seller_table = (array) $db_tables['seller'];
    $shipntracking_table = (array) $db_tables['shipntracking'];
    $business_table = (array)$db_tables['business'];
    $oms_table = (array)$db_tables['oms'];
    $catalog_table = (array)$db_tables['catalog'];

    $datas = [
        'web_table' => ($db_tables['web']),
        'inventory_table' => ($db_tables['inventory']),
        'order_table' => $db_tables['order'],
        'seller_table' => $db_tables['seller'],
        'shipntracking_table' => $db_tables['shipntracking'],
        'business_table' => $db_tables['business'],
        'oms_table' => $db_tables['oms'],

    ];


    foreach ($web_table as $key => $data) {
        $dat_web['web'][] = $data->Tables_in_mosh_360web;
    }
    foreach ($inventory_table as $key => $inv_data) {
        $data_inv['inventory_table'][] = $inv_data->Tables_in_mosh_inventory;
    }
    foreach ($order_table as $key => $ord_data) {
        $data_ord['order_table'][] = $ord_data->Tables_in_mosh_orders;
    }
    foreach ($seller_table as $key => $sell_data) {
        $data_seller['seller_table'][] = $sell_data->Tables_in_mosh_seller;
    }
    foreach ($shipntracking_table as $key => $ship_data) {
        $data_ship['shipntracking_table'][] = $ship_data->Tables_in_mosh_shipntrack;
    }
    foreach ($business_table as $key => $buis_data) {
        $data_busi['business_table'][] = $buis_data->Tables_in_mosh_business;
    }
    foreach ($oms_table as $key => $oms_data) {
        $data_oms['oms_table'][] = $oms_data->Tables_in_mosh_oms;
    }
    // foreach ($catalog_table as $key => $cat_data) {
    //     $data_cat['catalog_table'][] = $cat_data->Tables_in_mosh_catalog;
    // }
    foreach ($catalog_table as $key => $cat_data) {
        $data_cat['catalog_table'][] = $cat_data->Tables_in_mosh_catalog;
    }
    po($data_cat);
    $table_data = [
        'web' => $dat_web,
        'inventory' => $data_inv,
        'order' => $data_ord,
        'seller' => $data_seller,
        'shipntrack' => $data_ship,
        'business' => $data_busi,
        'oms' => $data_oms,
        'catalog' => $data_cat,
    ];
    // po($table_data);
    $mergedArray = array_merge($dat_web, $data_inv, $data_ord, $data_seller, $data_ship, $data_busi, $data_oms, $data_cat);
    po($mergedArray);
});

Route::get('config/test', function () {
    $datas =  Backup::where("status", 1)->get(["connection", "table_name"])->groupBy("connection");

    // $config = config('database.connections.inventory.dump.excludeTables');
    // dd($config);


    foreach ($datas as $connection => $table_names) {

        $table_names = collect($table_names)->pluck("table_name");

        // Config()->set(
        //     "database.connections.{$connection}.dump.excludeTables",
        //     $table_names
        // );

        $value = Config::get("database.connections.{$connection}.dump.excludeTables");
        po($value);
    }
});

Route::get('sanju/event', function () {
    event(new EventManager('hello world'));
});

Route::get('sanju/test/status', function () {
    $smsa_data =  SmsaTracking::query()
        ->select('activity')
        ->distinct()
        ->get();

    $courier_code =   Courier::query()->where('courier_name', 'SMSA')->select('id')->get();
    $code = $courier_code[0]->id;

    foreach ($smsa_data as $datas) {
        $data = [
            'courier_id' => $code,
            'courier_status' => $datas->activity
        ];
        StatusManagement::upsert($data, ['cp_status_cp_id_unique'], ['courier_id', 'courier_status']);
    }
});

Route::get('sanju/bbcreds/count', function () {

    $sources = ['in', 'us'];
    foreach ($sources as $source) {
        $value = Cache::get('creds_count');
        po($value[$source]);
        // foreach()

    }
    exit;
    $codes = ['in' => '11', 'us' => '4'];

    $counts = [];
    foreach ($codes as $key => $code) {
        $counts[$key] = Aws_credential::query()
            ->where(['mws_region_id' => $code])
            ->selectRaw('count(case when credential_priority = "1" then 1 end) as "1", count(case when credential_priority = "2" then 1 end) as "2",
            count(case when credential_priority = "3" then 1 end) as "3",   count(case when credential_priority = "4" then 1 end) as "4"')
            ->first()->toArray();
    }


    po($counts);
    foreach ($counts['in'] as $key1 => $count) {
        po($key1 + 1);
        po($count);
    }
});

route::get('sanju/feed/test', function () {
    // $feedback_id = 136083019467;
    $feedback_id = 136164019468;
    $store_id = 6;

    $country_code = 'IN';


    $url  = (new FeedOrderDetailsApp360())->getFeedStatus($feedback_id, $store_id, $country_code);

    if ($url) {

        $data = file_get_contents($url);

        $data_json = json_decode(json_encode(simplexml_load_string($data)), true);
        po($data_json);
    }
});

Route::get('price/push', 'SanjayTestController@pricepush')->name('sanjay.test');
Route::get('availability/push', 'SanjayTestController@availability_push')->name('sanjay.availability');
Route::get('price/feed/check/{feed_id}', 'SanjayTestController@feed_check')->name('sanjay.feed.test');
