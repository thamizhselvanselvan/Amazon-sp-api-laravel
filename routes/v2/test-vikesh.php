<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use League\Csv\Reader;
use League\Csv\Writer;
use Carbon\CarbonPeriod;
use App\Models\TestMongo;
use App\Models\MongoDB\zoho;
use App\Models\FileManagement;
use App\Models\Catalog\catalogae;
use App\Models\Catalog\catalogin;
use App\Models\Catalog\catalogsa;
use App\Models\Catalog\catalogus;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_us;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_source;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\Catalog\PriceConversion;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Modal;

Route::get('zoho/index', 'VikeshTestController@index');
Route::get('zoho/test', 'VikeshTestController@ReadZohoTextFile');

Route::get('zoho/dump', function () {
    $token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];

    $payload = [
        "query" => [
            "module" => "Leads",
            "page" => 1
        ]
    ];
    $url = "https://www.zohoapis.com/crm/bulk/v2/read";

    $headers = Http::withoutVerifying()->withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $token,
        "Content-Type" => "application/json"
    ])->post($url, $payload);

    $response = $headers->json();
    if (!Storage::exists('ZohoResponse/zoho-response1.txt')) {
        Storage::put('ZohoResponse/zoho-response1.txt', json_encode($response));
    }
    po($response);
});

Route::get('zoho/dump2', function () {
    $token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];
    $url = "https://www.zohoapis.com/crm/bulk/v2/read";

    $zohoResponse =  json_decode(Storage::get('ZohoResponse/zoho-response1.txt', true));
    $requestId = $zohoResponse->data[0]->details->id;

    $requestResponse = Http::withoutVerifying()->withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $token
    ])->get($url . "/" . $requestId);

    po($requestResponse->json());
    Storage::put('ZohoResponse/zoho-response2.txt', json_encode($requestResponse->json()));
    po($requestId);
});

Route::get('zoho/dump3', function () {

    // $timestamp = [
    //     'first_name' => 'vikesh1',
    //     'last_name' => 'kumar1'
    // ];

    $records = zoho::select(['ASIN', 'Alternate_Order_No', 'updated_at'])->orderBy('updated_at', 'DESC')->limit(100)->get()->toArray();

    po(($records));
    exit;

    $data = CSV_Reader('zohocsv/1929333000107582112.csv');
    $count = 0;
    $result = [];
    $asin = [];
    $order_no = [];

    foreach ($data as  $record) {

        $result[] = $record;
        $asin[] = $record['ASIN'];
        $order_no[] = $record['Alternate_Order_No'];
        $unique[] = [
            'ASIN' => $record['ASIN'],
            'Alternate_Order_No' => $record['Alternate_Order_No']
        ];
        // TestMongo::where('ASIN', $record['ASIN'])->where('Alternate_Order_No', $record['Alternate_Order_No'])->update($record, ['upsert' => true]);
        // po($asin);
        // DB::connection('mongodb')->collection('zoho')->updateMany('ASIN', ['$in' => $record['ASIN']], ['$set', $record], ['upsert' => true]);
        TestMongo::where('Alternate_Order_No_1_ASIN_1', $unique)->update($record, ['upsert' => true]);
        po($result);
        // if ($count == 10) {

        //     // TestMongo::insert($result);
        //     // TestMongo::where('ASIN', $asin)->where('Alternate_Order_No', $order_no)->update($result, ['upsert' => true]);
        //     $count = 0;
        //     $result = [];
        //     // exit;
        // }
        // $count++;
    }
    // TestMongo::insert($result);
    // TestMongo::whereIn('ASIN', $asin)->whereIn('Alternate_Order_No', $order_no)->update($result, ['upsert' => true]);
    // po($asin);
    po($order_no);
    exit;
    $zohoResponse =  json_decode(Storage::get('ZohoResponse/zoho-response2.txt', true));
    po($zohoResponse);
});

Route::get('export', function () {
    $chunk = 1000;
    $total =  DB::connection('catalog')->select("SELECT cat.asin 
    FROM asin_source_ins as source 
    RIGHT JOIN catalognewins as cat 
    ON cat.asin=source.asin 
    WHERE source.asin IS NULL
   ");

    $loop = ceil(count($total) / $chunk);
    for ($i = 0; $i < $loop; $i++) {

        $data =  DB::connection('catalog')->select("SELECT cat.asin 
        FROM asin_source_ins as source 
        RIGHT JOIN catalognewins as cat 
        ON cat.asin=source.asin 
        WHERE source.asin IS NULL
        LIMIT 1000");
        $asin = [];
        foreach ($data as $record) {
            $asin[] = [
                'asin' => $record->asin,
                'user_id' => '13',
                'status' => 0
            ];
        }
        $table = table_model_create(country_code: 'in', model: 'Asin_source', table_name: 'asin_source_');
        $table->upsert($asin, ['user_asin_unique'], ['asin', 'status']);
        Log::warning('successfully' . $i);
    }
});

Route::get('test', function () {
    $new_offer_lists = ['4', '3', '2', '3'];
    $highest_amount = min($new_offer_lists);
    po($highest_amount);
    po($new_offer_lists);
    $key = min(array_keys($new_offer_lists, min($new_offer_lists)));
    po(($key));
    // exit;



    exit;
    $date = Carbon::now()->addDays(105);
    $date1 = Carbon::now();
    if ($date <= $date1) {
        echo 'working';
    }
    po($date);
});
