<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Services\Zoho\ZohoApi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;
use Illuminate\Support\Facades\Storage;

Route::get('test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');

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
//eneric Keywords fetch
Route::get('sanju/test/cs', function () {
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

Route::get('sanju/zazil/tracking', function () {

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
Route::get('sanju/deletion/logic', function () {
    $files = Storage::files('Cliqnshop\asin_import');
    $tenDaysBefore = Carbon::now()->subDays(30);

    foreach ($files as $file) {

        $lastModified = Storage::lastModified($file);
        $lastModifiedTime = Carbon::createFromTimestamp($lastModified);

        if ($lastModifiedTime->lt($tenDaysBefore)) {
            Storage::delete($file);
        }
        po('deleted');
    }
});
