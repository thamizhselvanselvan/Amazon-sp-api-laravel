<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Services\Inventory\InventoryCsvImport;
use Google\Cloud\Translate\V2\TranslateClient;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::middleware('can:Admin')->group(function () {
    Route::get('test/catalog/{asin}/{country}', 'TestController@getASIN');
    Route::get('test/seller/order/{seller_id}/{country_code}', 'TestController@getSellerOrder');
    Route::get('test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');
    Route::get('renameamazoninvoice/', 'TestController@RenameAmazonInvoice');
    Route::get('getPricing/', 'TestController@GetPricing');

    Route::get('test/translation/{order_id}', function ($order_id) {

        $translate = new TranslateClient([
            'key' => config('app.google_translate_key'),
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
                            'target' => 'en',
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
                'Content-Type: text/plain',
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

    Route::match (['get', 'post'], 'test/zoho/webhook', 'TestController@zohoWebhookResponse');

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
                "method" => "post",
            ],
            'query' => [

                'module' => 'Leads',
                'page' => 1,
            ],
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

Route::get('aramex-test/{order_item_id}', function ($order_item_id) {

    $currentTimestamp = Carbon::now()->timestamp;
    $currentTimestampMilliseconds = $currentTimestamp * 1000;

    $order_details = DB::connection('order')
        ->select("SELECT
                oids.*,
                ord.amazon_order_identifier as order_id,
                ord.purchase_date as order_date,
                ord.payment_method_details as pay_method,
                oids.quantity_ordered as item,
                ord.buyer_info as mail
            FROM orders AS ord
            INNER join orderitemdetails AS oids
            ON ord.amazon_order_identifier = oids.amazon_order_identifier
            WHERE
            oids.order_item_identifier = $order_item_id
        ");
    // dd($order_details[0]);
    $shipping_address = json_decode($order_details[0]->shipping_address);
    $name = $shipping_address->Name;
    $phone = $shipping_address->Phone;
    $AddressLine1 = $shipping_address->AddressLine1;
    $AddressLine2 = $shipping_address->AddressLine2;
    $City = $shipping_address->City;
    $CountryCode = $shipping_address->CountryCode;

    // dd($shipping_address);

    $order_item_identifier = $order_details[0]->order_item_identifier;
    $item_name = $order_details[0]->title;
    $pieces = $order_details[0]->item ?? 1;
    $quantity = $order_details[0]->quantity_ordered ?? 1;
    $asin = $order_details[0]->asin;
    $cat_data = DB::connection('catalog')->select("SELECT dimensions FROM catalognewins  where asin = '$asin'");

    $dimensions = json_decode($cat_data[0]->dimensions);

    $package = $dimensions[0]->package;

    //  $hight_unit = $package->height->unit;
    //  $length_unit = $package->length->unit;
    //  $width_unit = $package->width->unit;

    $height = $package->height->value;

    $length = $package->length->value;

    $width = $package->width->value;

    $weight = $package->weight->value;

    //  dd($package);

    $url = 'https://ws.aramex.net/shippingapi.v2/shipping/service_1_0.svc/json/CreateShipments';
    $params = [
        'Shipments' => [
            [
                'Reference1' => $order_item_identifier,
                // 'Reference2' => null,
                // 'Reference3' => null,
                'Shipper' => [
                    // 'Reference1' => '',
                    // 'Reference2' => null,
                    'AccountNumber' => '60531487',
                    'PartyAddress' => [
                        'Line1' => 'Test Shipper Address Line1{{testname}}',
                        'Line2' => 'Test Shipper Address Line2{{testname}}',
                        'Line3' => '',
                        'City' => 'Mumbai',
                        'StateOrProvinceCode' => '',
                        'PostCode' => '400093',
                        'CountryCode' => 'IN',
                        'Longitude' => 0,
                        'Latitude' => 0,
                        'BuildingNumber' => null,
                        'BuildingName' => null,
                        'Floor' => null,
                        'Apartment' => null,
                        'POBox' => null,
                        'Description' => null,
                    ],
                    'Contact' => [
                        'Department' => null,
                        'PersonName' => 'Test Shipper Name',
                        'Title' => null,
                        'CompanyName' => 'Test Shipper Name/Test Shipper Company Name',
                        'PhoneNumber1' => '048707766',
                        'PhoneNumber1Ext' => '',
                        'PhoneNumber2' => '',
                        'PhoneNumber2Ext' => '',
                        'FaxNumber' => null,
                        'CellPhone' => '971556893100',
                        'EmailAddress' => 'test@aramex.com',
                        'Type' => '',
                    ],
                ],
                'Consignee' => [
                    'Reference1' => null,
                    'Reference2' => null,
                    'AccountNumber' => null,
                    'PartyAddress' => [
                        'Line1' => $AddressLine1,
                        'Line2' => $AddressLine2,
                        'Line3' => '',
                        'City' => $City,
                        //'StateOrProvinceCode' => 'FU',
                        'PostCode' => '',
                        'CountryCode' => $CountryCode,
                        'Longitude' => 0,
                        'Latitude' => 0,
                        'BuildingNumber' => null,
                        'BuildingName' => null,
                        'Floor' => null,
                        'Apartment' => null,
                        'POBox' => null,
                        'Description' => null,
                    ],
                    'Contact' => [
                        'Department' => null,
                        'PersonName' => $name,
                        'Title' => null,
                        'CompanyName' => $name,
                        'PhoneNumber1' => $phone,
                        'PhoneNumber1Ext' => '',
                        'PhoneNumber2' => '',
                        'PhoneNumber2Ext' => '',
                        'FaxNumber' => null,
                        'CellPhone' => $phone,
                        'EmailAddress' => '',
                        'Type' => '',
                    ],
                ],
                // 'ThirdParty' => [
                //     'AccountNumber' => '60531487',
                //     'PartyAddress' => [
                //         'Line1' => 'Test thirdparty Address Line1',
                //         'Line2' => 'Test thirdparty Address Line2',
                //         'Line3' => '',
                //         'City' => 'Dubai',
                //         'StateOrProvinceCode' => '',
                //         'PostCode' => '125212',
                //         'CountryCode' => 'AE',
                //         'Longitude' => 0,
                //         'Latitude' => 0,
                //         'BuildingNumber' => null,
                //         'BuildingName' => null,
                //         'Floor' => null,
                //         'Apartment' => null,
                //         'POBox' => null,
                //         'Description' => null,
                //     ],
                //     'Contact' => [
                //         'Department' => null,
                //         'PersonName' => 'Test third party Name',
                //         'Title' => null,
                //         'CompanyName' => 'Test third party/third party',
                //         'PhoneNumber1' => '04870776612',
                //         'PhoneNumber1Ext' => '',
                //         'PhoneNumber2' => '',
                //         'PhoneNumber2Ext' => '',
                //         'FaxNumber' => null,
                //         'CellPhone' => '971556893111',
                //         'EmailAddress' => 'test123@aramex.com',
                //         'Type' => '',
                //     ],
                // ],
                'ShippingDateTime' => '/Date(' . $currentTimestampMilliseconds . ')/',
                'DueDate' => '/Date(' . $currentTimestampMilliseconds . ')/',
                'Comments' => null,
                'PickupLocation' => null,
                'OperationsInstructions' => null,
                'AccountingInstrcutions' => null,
                'Details' => [
                    'Dimensions' => [
                        'Length' => round($length * 2.54),
                        'Width' => round($width * 2.54),
                        'Height' => round($height * 2.54),
                        'Unit' => 'CM',
                    ],
                    'ActualWeight' => [
                        'Unit' => 'LB',
                        'Value' => $weight,
                    ],
                    'ChargeableWeight' => [
                        'Unit' => 'LB',
                        'Value' => 0,
                    ],
                    'DescriptionOfGoods' => $item_name,
                    'GoodsOriginCountry' => 'IN',
                    'NumberOfPieces' => $pieces,
                    'ProductGroup' => 'DOM',
                    'ProductType' => 'OND',
                    'PaymentType' => 'P',
                    'PaymentOptions' => 'ACCT',
                    // 'CustomsValueAmount' => [
                    //     'CurrencyCode' => 'AED',
                    //     'Value' => 10,
                    // ],
                    // 'CashOnDeliveryAmount' => [
                    //     'CurrencyCode' => 'AED',
                    //     'Value' => 0,
                    // ],
                    // 'InsuranceAmount' => [
                    //     'CurrencyCode' => 'AED',
                    //     'Value' => 0,
                    // ],
                    'CashAdditionalAmount' => [
                        'CurrencyCode' => 'INR',
                        'Value' => 0,
                    ],
                    // 'CashAdditionalAmountDescription' => null,
                    // 'CollectAmount' => [
                    //     'CurrencyCode' => 'AED',
                    //     'Value' => 0,
                    // ],
                    'Services' => '',
                    // 'Items' => [
                    //     [
                    //         'PackageType' => 'item',
                    //         'Quantity' => $quantity,
                    //         'Weight' => [
                    //             'Unit' => 'CM',
                    //             'Value' => 0,
                    //         ],
                    //         'Comments' => 'no description',
                    //         'Reference' => 'no barcode',
                    //         'PiecesDimensions' => null,
                    //         'CommodityCode' => null,
                    //         'GoodsDescription' => null,
                    //         'CountryOfOrigin' => null,
                    //         'CustomsValue' => null,
                    //         'ContainerNumber' => null,
                    //     ],
                    // ],
                    // 'DeliveryInstructions' => null,
                    // 'AdditionalProperties' => null,
                    // 'ContainsDangerousGoods' => false,
                ],
                // 'Attachments' => null,
                // 'ForeignHAWB' => null,
                // 'TransportType' => 0,
                // 'PickupGUID' => null,
                // 'Number' => null,
                // 'ScheduledDelivery' => null,
            ],
        ],
        'LabelInfo' => [
            'ReportID' => 9729, //9201
            'ReportType' => 'URL',
        ],
        'ClientInfo' => [
            'UserName' => 'mp@moshecom.com',
            'Password' => 'A#mazon170',
            'Version' => 'v1.0',
            'AccountNumber' => '60531487',
            'AccountPin' => '654654',
            'AccountEntity' => 'BOM',
            'AccountCountryCode' => 'IN',
            'Source' => 24,
            'PreferredLanguageCode' => null,
        ],
        // 'Transaction' => null,
    ];

    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, $params);

        $result = json_decode($response->body(), true);

        if ($result['HasErrors'] == true) {
            $error_code = $result['Shipments'][0]['Notifications'][0]['Code'];
            $message = $result['Shipments'][0]['Notifications'][0]['Message'];
            Log::warning($error_code . '  ' . $message);
        }

        dd($result);

    } catch (\Exception $e) {
        // Handle the exception
        return $e->getMessage();
    }

});

Route::get('smsa-test/{order_item_id}', function ($order_item_id) {

    $currentTimestamp = Carbon::now()->timestamp;
    // $currentTimestampMilliseconds = $currentTimestamp * 1000;

    $order_details = DB::connection('order')
        ->select("SELECT
                oids.*,
                ord.amazon_order_identifier as order_id,
                ord.purchase_date as order_date,
                ord.payment_method_details as pay_method,
                oids.quantity_ordered as item,
                ord.buyer_info as mail
            FROM orders AS ord
            INNER join orderitemdetails AS oids
            ON ord.amazon_order_identifier = oids.amazon_order_identifier
            WHERE
            oids.order_item_identifier = $order_item_id
        ");

    // dd($order_details[0]);
    $shipping_address = json_decode($order_details[0]->shipping_address);
    $name = $shipping_address->Name;
    $phone = $shipping_address->Phone;
    $AddressLine1 = $shipping_address->AddressLine1;
    $AddressLine2 = $shipping_address->AddressLine2;
    $City = $shipping_address->City;
    $CountryCode = $shipping_address->CountryCode;

    // dd($shipping_address);

    $order_item_identifier = $order_details[0]->order_item_identifier;
    $item_name = $order_details[0]->title;
    $pieces = $order_details[0]->item ?? 1;
    // $quantity = $order_details[0]->quantity_ordered ?? 1;
    $asin = $order_details[0]->asin;
    $cat_data = DB::connection('catalog')->select("SELECT dimensions FROM catalognewins  where asin = '$asin'");

    $dimensions = json_decode($cat_data[0]->dimensions);

    $package = $dimensions[0]->package;

    //  $hight_unit = $package->height->unit;
    //  $length_unit = $package->length->unit;
    //  $width_unit = $package->width->unit;

    // $height = $package->height->value;

    // $length = $package->length->value;

    // $width = $package->width->value;

    $weight = $package->weight->value;


    $url = 'https://track.smsaexpress.com/SecomRestWebApi/api/addship';

    $params = [
        "passkey" => "", //Mah@8537
        "refno" => $order_item_identifier,
        "sentDate" => "/Date(' . $currentTimestamp . ')/",
        "idNo" => "",
        "cName" => $name,
        "cntry" => $CountryCode,
        "cCity" => $City,
        "cZip" => "",
        "cPOBox" => "",
        "cMobile" => $phone,
        "cTel1" => "",
        "cTel2" => "",
        "cAddr1" =>  $AddressLine1,
        "cAddr2" => $AddressLine2,
        "shipType" => "DLV",
        "PCs" => $pieces,
        "cEmail" => "",
        "carrValue" => "",
        "carrCurr" => "",
        "codAmt" => "",
        "weight" => $weight,
        "itemDesc" => $item_name,
        "custVal" => "",
        "custCurr" => "",
        "insrAmt" => "",
        "insrCurr" => "",
        "sName" => "test shipper name",
        "sContact" => "4863215632",
        "sAddr1" => "test shipper address line 1",
        "sAddr2" => "test shipper address line 2 o",
        "sCity" => "shipper city",
        "sPhone" => "123456212365",
        "sCntry" => "AE",
        "prefDelvDate" => "",
        "gpsPoints" => "",
    ];

    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, $params);

        $result = json_decode($response->body(), true);

        dd($result);

    } catch (\Exception $e) {
        // Handle the exception
        return $e->getMessage();
    }

});
