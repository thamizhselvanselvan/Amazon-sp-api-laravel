<?php

use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use League\Csv\Writer;
use App\Models\Mws_region;
use Smalot\PdfParser\Parser;
use App\Services\Zoho\ZohoApi;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use function Clue\StreamFilter\fun;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;

use App\Models\order\OrderUpdateDetail;
use App\Models\seller\AsinMasterSeller;
use Illuminate\Support\Facades\Storage;
use App\Models\seller\SellerAsinDetails;
use App\Services\SP_API\API\Order\Order;
use Illuminate\Support\Facades\Response;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\API\Order\OrderItem;
use App\Services\Inventory\InventoryCsvImport;
use Google\Cloud\Translate\V2\TranslateClient;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use App\Services\Catalog\AllPriceExportCsvServices;
use App\Services\Courier_Booking\B2cshipBookingServices;
use App\Services\SP_API\API\Order\OrderUsingRedBean;
use App\Services\SP_API\API\Order\CheckStoreCredServices;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::middleware('can:Admin')->group(function () {
    Route::get('test/catalog/{asin}/{country}', 'TestController@getASIN');
    Route::get('test/seller/order/{seller_id}/{country_code}', 'TestController@getSellerOrder');
    Route::get('test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');
    Route::get('renameamazoninvoice/', 'TestController@RenameAmazonInvoice');
    Route::get('getPricing/', 'TestController@GetPricing');

    Route::get('test/translation/{order_id}', function ($order_id) {


        $translate = new TranslateClient([
            'key' => config('app.google_translate_key')
        ]);

        $address = OrderItemDetails::select('shipping_address')
            ->where('amazon_order_identifier', $order_id)
            ->get()
            ->toArray();

        $arabicToEnglish = [];
        if ($address != null) {

            $ship_address = json_encode($address[0]['shipping_address']);
            $arabic_lang = preg_match("/u06/", $ship_address);
            if ($arabic_lang == 1) {
                $records = json_decode($ship_address);
                po($records);
                foreach ($records as $key => $record) {
                    if (preg_match('/u06/', json_encode($record)) == 1) {

                        $translatedText = $translate->translate($record, [
                            'target' => 'en'
                        ]);
                        $arabicToEnglish[$key] = $translatedText['text'];
                    }
                }
            }
        }
        po($arabicToEnglish);
    });

    Route::get('ustoinb2c/{weight}/{price}', 'TestController@USAToINDb2c');
    Route::get('ustoinb2b/{weight}/{price}', 'TestController@USAToINDb2b');
    Route::get('ustouae/{weight}/{price}', 'TestController@USAToUAE');
    Route::get('ustosg/{weight}/{price}', 'TestController@USATOSG');

    Route::get('ustoinprice', function () {
        $bb_price = 9.98;
        $weight = 0.27;
        if ($weight > 0.9) {

            $int_shipping_base_charge = (6 + ($weight - 1) * 6);
        } else {

            $int_shipping_base_charge = 6;
        }
        $duty_rate = 32.00 / 100;
        $seller_commission = 10 / 100;
        $packaging = 2;
        $amazon_commission = 22.00 / 100;

        $ex_rate = 82;
        $duty_cost = round(($duty_rate * ($bb_price + $int_shipping_base_charge)), 2);

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $seller_commission);

        $usd_sp = round($price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * 0.12), 2);

        $india_sp = $usd_sp * $ex_rate;
        po($india_sp);
    });

    Route::get('intosa/{weight}/{price}', 'TestController@INToSA');
    Route::get('intouae/{weight}/{price}', 'TestController@INDToUAE');
    Route::get('INDToSG/{weight}/{price}', 'TestController@INDToSG');

    Route::get('smsatracking/{awb}', function ($awb_no) {
        return SmsaTrackingResponse($awb_no);
    });

    Route::get('test/api', function () {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://uat-api.b2cship.us/PacificAmazonAPI.svc/TrackingAmazon',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<?xml version="1.0" encoding="UTF-8"?>
<AmazonTrackingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
<Validation>
<UserID>Amazon</UserID>
<Password>AcZmraDzLoxA4NxLUcyrWnSiEaXxRQkfJ9B5hCbiK5M=</Password>
</Validation>
<APIVersion>1.0</APIVersion>
<TrackingNumber>US10000142</TrackingNumber>
</AmazonTrackingRequest>
',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain'
            ),
        ));

        $response = curl_exec($curl);

        if ($response === false) {
            echo 'Curl error: ' . curl_error($curl);
        }

        curl_close($curl);
        echo $response;
    });


    Route::get('test/order', 'TestController@testOrderAPI');
    Route::get('search_catalog/{country_code}', 'TestController@searchCatalog');

    Route::get('pricing', 'TestController@PricingTest');

    Route::get('test/zoho', 'TestController@TestZoho');
    Route::get('test/get/zoho/data/{lead}', 'TestController@TestGetZoho');

    Route::get('test/smsa/booking', 'Testcontroller@SmsaBooking');

    Route::get('test/amazon-feed/{lead_id}/{seller_id}', 'TestController@TestAmazonFeed');

    Route::get('test/emirate/tracking/{tracking_id}', 'TestController@emiratePostTracking');
    Route::get('test/emirate/booking', 'TestController@emiratePostBooking');

    Route::get('test/aramex/booking', 'TestController@AramexBooking');
    Route::get('test/aramex/tracking/{tracking_id}', 'TestController@AramexTracking');

    Route::get('test/download-file/{path}', function ($path) {

        $path_array = explode("'", $path);

        $file_path = '';
        foreach ($path_array as $name) {
            $file_path .= $name . '/';
        }

        $file_path = rtrim($file_path, "/");

        return Storage::download($file_path);
    });

    Route::match(['get', 'post'], 'test/zoho/webhook', 'TestController@zohoWebhookResponse');

    Route::get('test/inventory', function () {
        $filePath = Storage::path('zoho_csv');
        echo $filePath;
        exit;

        (new InventoryCsvImport())->index('Inventory_CSV/Inventory2023-01-03-13-46-12.csv');
        //
    });

    Route::get('test/zoho/read', function () {

        $token = json_decode(Storage::get('zoho/access_token.txt'), true)['access_token'];

        $url = 'https://www.zohoapis.com/crm/bulk/v2/read';

        $payload = [
            "callback" => [
                "url" => "https://catalog-manager-mosh.com/api/test/zoho/webhook",
                "method" => "post"
            ],
            'query' => [

                'module' => 'Leads',
                'page' => 1
            ]
        ];

        // $response = Http::withoutVerifying()
        //     ->withHeaders([
        //         'Authorization' => 'Zoho-oauthtoken ' . $token,
        //         'Content-Type' => 'application/json'
        //     ])->post($url, $payload);

        // po($response->json());

        // exit;

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $token,
        ])->get('https://www.zohoapis.com/crm/bulk/v2/read/1929333000107167065/result');


        Storage::put('zohocsv/1929333000107167065.zip', $response);
        po($response->json());
        exit;

        echo 'success';
    });
});
